<?php

/**
 * current-screen-id-helper.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Set current screen identifier class
 *
 * @access public
 */
final class CurrentScreenIdHelper
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Saved alternative identifier
     *
     * @access private
     * @var string
     */
    private $savedAlternativeId = '';

    /**
     * Saved script name
     *
     * @access private
     * @var string
     */
    private $savedScriptName = '';

    /**
     * Get singleton instance
     *
     * @access public
     * @return object Singleton instance
     */
    public static function getInstance()
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self ();
        }
        // exit
        return self::$instance;
    }

    /**
     * Callback for parameter "post_type"
     *
     * @access private
     * @param string $value
     *            Value of parameter "post_type"
     * @param int $postValue
     *            Value of parameter "post"
     * @return string Parsed value of parameter "post_type"
     */
    private function callbackParameterPostType($value, $postValue)
    {
        // exit
        return (isset ($value [0]) /* strlen($value) > 0 */) ? $value : ((isset ($postValue [0]) /* strlen($postValue) > 0 */) ? get_post_type($postValue) : 'post');
    }

    /**
     * Callback for parameter "taxonomy"
     *
     * @access private
     * @param string $value
     *            Value of parameter "taxonomy"
     * @return string Parsed value of parameter "taxonomy"
     */
    private function callbackParameterTaxonomy($value)
    {
        // exit
        return $value;
    }

    /**
     * Parse parameters for identifier
     *
     * @access private
     * @param string $id
     *            Identifier
     * @return string Parsed identifier
     */
    private function parseParameters($id)
    {
        // set data for screen identifier
        $parsCallbacks = array(
            'post_type' => array(
                'callback' => array(
                    $this,
                    'callbackParameterPostType'
                ),
                'additionalparameters' => array(
                    'post'
                )
            ),
            'taxonomy' => array(
                'callback' => array(
                    $this,
                    'callbackParameterTaxonomy'
                ),
                'additionalparameters' => array()
            )
        );
        // parse parameters
        foreach ($parsCallbacks as $parameter => $data) {
            if (strpos($id, '{' . $parameter . '}') !== false) {
                // get parameters values
                $parsNames = array_merge(array(
                    $parameter
                ), $data ['additionalparameters']);
                $parsValues = array();
                foreach ($parsNames as $name) {
                    $parsValues [] = (isset ($_GET [$name])) ? sanitize_text_field($_GET [$name]) : '';
                }
                // replace tag
                $id = str_replace('{' . $parameter . '}', call_user_func_array($data ['callback'], $parsValues), $id);
            }
        }
        // exit
        return $id;
    }

    /**
     * Get current screen identifier; it can do it even if function "get_current_screen" returns NULL
     *
     * @access public
     * @return array Current screen identifier or empty string if function "get_current_screen" returns NULL (in element "original") and alternative screen identifier which is almost always available (in element "alternative") or empty array if it is not an administration panel
     */
    public function get()
    {
        // initialize
        $originalId = '';
        $alternativeId = '';
        $scriptName = '';
        // check if it is administration panel
        if ((is_admin()) || (is_network_admin())) {
            // optionally get remembered values
            if (isset ($this->savedScriptName [0]) /* strlen($this->savedScriptName) > 0 */) {
                $alternativeId = $this->savedAlternativeId;
                $scriptName = $this->savedScriptName;
            } else {
                // prepare identifiers list
                $ids = array(
                    'index.php' => 'dashboard',
                    'edit.php' => 'edit-{post_type}',
                    'post-new.php' => '{post_type}',
                    'edit-tags.php' => 'edit-{taxonomy}',
                    'post.php' => '{post_type}',
                    'media-new.php' => 'media',
                    'link-add.php' => 'link',
                    'user-new.php' => 'user'
                );
                // get script name
                $div = explode('/', Helper::getInstance()->getScriptUrl());
                $scriptName = $div [count($div) - 1];
                if (!isset ($scriptName [0]) /* strlen($scriptName) === 0 */) {
                    $scriptName = 'index.php';
                }
                // get screen identifier
                if (isset ($ids [$scriptName])) {
                    // parse identifier
                    $alternativeId = $this->parseParameters($ids [$scriptName]);
                } else {
                    // get identifier based on script name
                    $div = explode('.', $scriptName);
                    $alternativeId = $div [0];
                }
                // optionally generate identifier based on "page" parameter
                if (isset ($_GET ['page'])) {
                    $pageValue = sanitize_text_field($_GET ['page']);
                    if ((isset ($pageValue [0]) /* strlen($pageValue) > 0 */) && (substr($pageValue, -4) === '.php')) {
                        $pageValue = substr($pageValue, 0, -4);
                    }
                    $idsPrefixesWithPage = array(
                        'edit.php' => '{post_type}s',
                        'upload.php' => 'media',
                        'edit-tags.php' => 'admin',
                        'edit-comments.php' => 'comments',
                        'themes.php' => 'appearance',
                        'options-general.php' => 'settings'
                    );
                    if (is_network_admin()) {
                        $idsPrefixesWithPage = array_merge($idsPrefixesWithPage, array(
                            'index.php' => 'index',
                            'themes.php' => 'themes'
                        ));
                    }
                    if (isset ($idsPrefixesWithPage [$scriptName])) {
                        $alternativeId = $this->parseParameters($idsPrefixesWithPage [$scriptName]);
                    }
                    $alternativeId .= '_page_' . $pageValue;
                }
                // optionally add network suffix
                if (is_network_admin()) {
                    $alternativeId .= '-network';
                }
                // get original screen identifier
                if (function_exists('get_current_screen')) {
                    $screen = get_current_screen();
                    $originalId = $screen->id;
                }
            }
            // remember some data
            $this->savedAlternativeId = $alternativeId;
            $this->savedScriptName = $scriptName;
        }
        // exit
        return array(
            'original' => $originalId,
            'alternative' => $alternativeId,
            'scriptname' => $scriptName
        );
    }
}
