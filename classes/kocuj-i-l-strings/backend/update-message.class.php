<?php

/**
 * update-message.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\KocujILStrings\Backend;

// set namespaces aliases
use KocujIL\V13a\Interfaces\Strings;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * \KocujIL\V13a\Classes\Project\Components\Backend\UpdateMessage classes strings
 *
 * @access public
 */
class UpdateMessage implements Strings
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Disable cloning of object
     *
     * @access private
     * @return void
     */
    private function __clone()
    {
    }

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
            self::$instance = new self();
        }
        // exit
        return self::$instance;
    }

    /**
     * Get string
     *
     * @access public
     * @param string $id
     *            String id
     * @return string Output string
     */
    public function getString($id)
    {
        // get string
        $texts = array(
            /* translators: %s: Version number */
            'ACTION_ADMIN_INIT_WINDOW_TITLE' => __('Changes in %s version', 'kocuj-sitemap'),
            /* translators: 1: plugin name, 2: beginning of the link to update information, 3: ending of the link to update information */
            'ACTION_ADMIN_INIT_TOP_MESSAGE_PLUGIN' => __('There are some new information about new version of the %1$s plugin. Click %2$shere%3$s to see these information.',
                'kocuj-sitemap'),
            /* translators: 1: theme name, 2: beginning of the link to update information, 3: ending of the link to update information */
            'ACTION_ADMIN_INIT_TOP_MESSAGE_THEME' => __('There are some new information about new version of the %1$s theme. Click %2$shere%3$s to see these information.',
                'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
