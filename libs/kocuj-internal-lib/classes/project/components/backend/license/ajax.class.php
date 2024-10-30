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
namespace KocujIL\V13a\Classes\Project\Components\Backend\License;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\HtmlHelper;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * License AJAX class
 *
 * @access public
 */
class Ajax extends ComponentObject
{

    /**
     * Action for displaying license
     *
     * @access public
     * @return void
     */
    public function actionAjaxDisplay()
    {
        // check AJAX nonce
        check_ajax_referer(Helper::getInstance()->getPrefix() . '__license', 'security');
        // display license
        $licenseFilename = $this->getComponent('license', ProjectCategory::BACKEND)->getLicenseFilename();
        if (isset ($licenseFilename [0]) /* strlen($licenseFilename) > 0 */) {
            $licenseFilename = $this->getComponent('dirs')->getProjectDir() . DIRECTORY_SEPARATOR . $licenseFilename;
            if (is_file($licenseFilename)) {
                $licenseText = file($licenseFilename);
                if (!empty ($licenseText)) {
                    $lastLineEmpty = false;
                    $lastLineStrong = false;
                    foreach ($licenseText as $val) {
                        $line = trim($val);
                        if (strlen($line) === 0) {
                            $lastLineEmpty = true;
                            echo '<br />';
                            $lastLineStrong = false;
                        } else {
                            if ((isset ($line [1]) /* strlen($line) > 1 */) && (substr($line, 0, 2) === '--')) {
                                echo '<br /><hr /><br />';
                                $lastLineStrong = false;
                            } else {
                                if ($lastLineEmpty) {
                                    echo '<br />';
                                } else {
                                    echo ' ';
                                }
                                if (((isset ($line [6]) /* strlen($line) > 6 */) && (substr($line, 0,
                                                7) === 'http://')) || ((isset ($line [7]) /* strlen($line) > 7 */) && (substr($line,
                                                0, 8) === 'https://'))) {
                                    echo HtmlHelper::getInstance()->getLink($line, '', array(
                                        'external' => true
                                    ));
                                    $lastLineStrong = false;
                                } else {
                                    if ($line === mb_convert_case($line, MB_CASE_UPPER, 'UTF-8')) {
                                        echo '<strong>' . $line . '</strong>';
                                        $lastLineStrong = true;
                                    } else {
                                        echo ($lastLineStrong ? '<br />' : '') . $line;
                                        $lastLineStrong = false;
                                    }
                                }
                            }
                            $lastLineEmpty = false;
                        }
                    }
                }
            }
        }
        // close connection
        wp_die();
    }
}
