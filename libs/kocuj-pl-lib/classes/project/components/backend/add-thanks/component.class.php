<?php

/**
 * component.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib\kocuj_pl_lib
 */

// set namespace
namespace KocujPlLib\V13a\Classes\Project\Components\Backend\AddThanks;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Enums\CheckJavascript;
use KocujIL\V13a\Enums\Project\Components\All\Window\Type;
use KocujPlLib\V13a\Classes\Helper;
use KocujPlLib\V13a\Classes\LibUrls;
use KocujPlLib\V13a\Classes\Version;
use KocujPlLib\V13a\Enums\Project\Components\Backend\AddThanks\Display;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Adding thanks class
 *
 * @access public
 */
class Component extends KocujIL\Classes\ComponentObject
{

    /**
     * Display adding thanks or not; must be one of the following constants from \KocujPlLib\V13a\Enums\Project\Components\Backend\AddThanks\Display: NO (when adding thanks will not be displayed) or YES (when adding thanks will be displayed)
     *
     * @access private
     * @var bool
     */
    private $display = Display::NO;

    /**
     * Get option name for adding thanks
     *
     * @access public
     * @return string Option name for adding thanks
     */
    public function getOptionNameThanksAdded()
    {
        // exit
        return 'thanks_added';
    }

    /**
     * Set adding thanks to display (true) or not (false)
     *
     * @access public
     * @param int $display
     *            Display adding thanks or not; must be one of the following constants from \KocujPlLib\V13a\Enums\Project\Components\Backend\AddThanks\Display: NO (when adding thanks will not be displayed) or YES (when adding thanks will be displayed)
     * @return void
     */
    public function setAddThanksDisplay($display)
    {
        // set value
        $this->display = $display;
        // add scripts
        if ($display === Display::YES) {
            JsHelper::getInstance()->addHelperJs();
            if (KocujIL\Classes\Helper::getInstance()->checkDebug(CheckJavascript::YES)) {
                JsHelper::getInstance()->addExceptionJs();
            }
            $this->getProjectObj()->getProjectKocujILObj()->get('js-ajax',
                KocujIL\Enums\ProjectCategory::ALL)->addAjaxJs();
            if (KocujIL\Classes\Helper::getInstance()->checkDebug(CheckJavascript::YES)) {
                JsHelper::getInstance()->addScript(Helper::getInstance()->getPrefix() . '-exception-code',
                    LibUrls::getInstance()->get('js'), 'exception-code', array(), array(),
                    Version::getInstance()->getVersion(), true);
                JsHelper::getInstance()->addScript(Helper::getInstance()->getPrefix() . '-exception',
                    LibUrls::getInstance()->get('js'), 'exception', array(), array(
                        KocujIL\Classes\Helper::getInstance()->getPrefix() . '-exception',
                        Helper::getInstance()->getPrefix() . '-exception-code'
                    ), Version::getInstance()->getVersion(), true);
            }
        }
    }

    /**
     * Get if thanks has been added (true) or not (false)
     *
     * @access public
     * @return bool Thanks has been added (true) or not (false)
     */
    public function getThanksAddedOptionValue()
    {
        // exit
        return ($this->getProjectObj()->getProjectKocujILObj()->get('meta')->get($this->getOptionNameThanksAdded()) === false) ? false : true;
    }

    /**
     * Action for adding JavaScript scripts
     *
     * @access public
     * @return void
     */
    public function actionAdminFooter()
    {
        // add scripts
        if ((!$this->getThanksAddedOptionValue()) && ($this->display === Display::YES)) {
            // initialize nonce
            $nonce = wp_create_nonce(Helper::getInstance()->getPrefix() . '__add_thanks');
            // add window
            $this->getProjectObj()->getProjectKocujILObj()->get('window',
                KocujIL\Enums\ProjectCategory::ALL)->addWindow('add_thanks', $this->getStrings('add-thanks',
                KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_WINDOW_TITLE'), 400, 400,
                Type::AJAX, array(
                    'url' => admin_url('admin-ajax.php'),
                    'ajaxdata' => array(
                        'action' => $this->getComponent('project-helper')->getPrefix() . '__add_thanks_more_info_window_display',
                        'security' => $nonce
                    )
                ));
            // add scripts
            JsHelper::getInstance()->addScript(Helper::getInstance()->getPrefix() . '-backend-add-thanks',
                LibUrls::getInstance()->get('js') . '/project/components/backend/add-thanks',
                'add-thanks', array(
                    'helper'
                ), array(
                    KocujIL\Classes\Helper::getInstance()->getPrefix() . '-all-js-ajax',
                    KocujIL\Classes\Helper::getInstance()->getPrefix() . '-all-window'
                ), Version::getInstance()->getVersion(), true, true,
                'kocujPLV13aBackendAddThanksVals', array(
                    'prefix' => Helper::getInstance()->getPrefix(),
                    'prefixKocujIL' => KocujIL\Classes\Helper::getInstance()->getPrefix(),
                    'security' => $nonce,
                    'websiteUrl' => get_home_url(),
                    'websiteTitle' => get_bloginfo('name', 'display'),
                    'websiteDescription' => get_bloginfo('description', 'display'),
                    'textMoreInfoLink' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_MORE_INFO_LINK'),
                    'textSending' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_SENDING'),
                    'textError' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_ERROR'),
                    'textErrorNoRetries' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_ERROR_NO_RETRIES'),
                    'textErrorAlreadyExists' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_ERROR_ALREADY_EXISTS'),
                    'textErrorWrongResponse1' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_ERROR_WRONG_RESPONSE_1'),
                    'textErrorWrongResponse2' => ($this->getProjectObj()->getProjectKocujILObj()->getMainSettingType() === KocujIL\Enums\ProjectType::PLUGIN) ? $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_ERROR_WRONG_RESPONSE_2_PLUGIN') : $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_ERROR_WRONG_RESPONSE_2_THEME'),
                    'textSuccess' => $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_ADMIN_FOOTER_SCRIPT_SUCCESS'),
                    'imageLoadingUrl' => LibUrls::getInstance()->get('images') . '/project/components/backend/add-thanks/loading.gif'
                ), true);
        }
    }
}
