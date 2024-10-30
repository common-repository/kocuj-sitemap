<?php

/**
 * ajax.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\Backend\UpdateMessage;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Update message AJAX class
 *
 * @access public
 */
class Ajax extends ComponentObject
{

    /**
     * Action for displaying update message
     *
     * @access public
     * @return void
     */
    public function actionAjaxDisplay()
    {
        // check AJAX nonce
        check_ajax_referer(Helper::getInstance()->getPrefix() . '__version_info', 'security');
        // check versions of project
        if ((!isset ($_POST ['projectVersionFrom'])) || (!isset ($_POST ['projectVersionTo']))) {
            wp_die();
        }
        // get information
        $information = $this->getComponent('update-message',
            ProjectCategory::BACKEND)->getMessage(sanitize_text_field($_POST ['projectVersionFrom']));
        // show information
        if ($information !== false) {
            echo str_replace('%2$s', sanitize_text_field($_POST ['projectVersionTo']),
                str_replace('%1$s', sanitize_text_field($_POST ['projectVersionFrom']), $information));
        }
        // close message
        $this->actionAjaxClose();
    }

    /**
     * Action for close update message
     *
     * @access public
     * @return void
     */
    public function actionAjaxClose()
    {
        // check AJAX nonce
        check_ajax_referer(Helper::getInstance()->getPrefix() . '__version_info', 'security');
        // close message
        $this->getComponent('meta')->addOrUpdate(Component::getOptionNameLastUpdateMessageVersion(),
            $this->getComponent('version', ProjectCategory::ALL)->getCurrentVersion());
        // close connection
        wp_die();
    }
}
