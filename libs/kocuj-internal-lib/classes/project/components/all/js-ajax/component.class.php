<?php

/**
 * component.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\All\JsAjax;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\JsHelper;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Configuration class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Add "js-ajax.js" script
     *
     * @access public
     * @return void
     */
    public function addAjaxJs()
    {
        // add script
        JsHelper::getInstance()->addLibScript('all-js-ajax', 'project/components/all/js-ajax',
            'js-ajax', array(
                'helper'
            ), array(), 'kocujILV13aAllJsAjaxVals', array(
                'prefix' => Helper::getInstance()->getPrefix(),
                'security' => wp_create_nonce(Helper::getInstance()->getPrefix() . '__js_ajax'),
                'canUseProxy' => (ini_get('allow_url_fopen') === '1') ? '1' : '0',
                'ajaxUrl' => admin_url('admin-ajax.php')
            ));
    }
}
