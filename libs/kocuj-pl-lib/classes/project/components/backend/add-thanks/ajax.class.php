<?php

/**
 * ajax.class.php
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
use KocujPlLib\V13a\Classes\Helper;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Adding thanks AJAX class
 *
 * @access public
 */
class Ajax extends KocujIL\Classes\ComponentObject
{

    /**
     * Action for set that adding thanks has been finished
     *
     * @access public
     * @return void
     */
    public function actionAjaxFinished()
    {
        // check AJAX nonce
        check_ajax_referer(Helper::getInstance()->getPrefix() . '__add_thanks', 'security');
        // set that adding thanks has been finished
        $this->getProjectObj()->getProjectKocujILObj()->get('meta')->addOrUpdate($this->getComponent('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getOptionNameThanksAdded(), '1');
        // close connection
        wp_die();
    }

    /**
     * Action for displaying more information about adding thanks in window
     *
     * @access public
     * @return void
     */
    public function actionAjaxMoreInfoWindowDisplay()
    {
        // check AJAX nonce
        check_ajax_referer(Helper::getInstance()->getPrefix() . '__add_thanks', 'security');
        // get list of websites where website address can be displayed
        $websites = array_merge(array(
            Helper::getInstance()->getKocujPlUrl()
        ), $this->getProjectObj()->getSettingArray('additionalwebsites'));
        $websitesString = '';
        $loopCount = count($websites);
        for ($z = 0; $z < $loopCount; $z++) {
            if ($z > 0) {
                $websitesString .= (($z < $loopCount - 1) ? ', ' : ' ' . $this->getStrings('add-thanks',
                        KocujIL\Enums\ProjectCategory::BACKEND)->getString('AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_WEBSITES_AND') . ' ');
            }
            $websitesString .= '<a href="' . esc_url('http://' . $websites [$z]) . '" target="_blank">' . $websites [$z] . '</a>';
        }
        // show more information about adding thanks
        printf($this->getStrings('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString(($this->getProjectObj()->getProjectKocujILObj()->getMainSettingType() === KocujIL\Enums\ProjectType::PLUGIN) ? 'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_1_PLUGIN' : 'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_1_THEME'),
            $websitesString);
        echo '<br /><br />';
        echo $this->getStrings('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString('AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_2');
        echo '<ul style="list-style:disc;margin-left:15px;">';
        echo '<li>';
        echo $this->getStrings('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString('AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_3');
        echo '</li><li>';
        echo $this->getStrings('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString('AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_4');
        echo '</li><li>';
        echo $this->getStrings('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString('AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_5');
        echo '</li>';
        echo '</ul>';
        echo $this->getStrings('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString('AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_6');
        // close connection
        wp_die();
    }
}
