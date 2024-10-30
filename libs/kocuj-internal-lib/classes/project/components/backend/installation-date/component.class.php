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
namespace KocujIL\V13a\Classes\Project\Components\Backend\InstallationDate;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Installation date class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Get option name for installation date
     *
     * @access public
     * @return string Option name for installation date
     */
    public static function getOptionNameInstallDate()
    {
        // exit
        return 'install_date';
    }

    /**
     * Get installation date from database
     *
     * @access public
     * @return string Installation date from database
     */
    public function getInstallationDateOptionValue()
    {
        // exit
        return $this->getComponent('meta')->get(self::getOptionNameInstallDate());
    }
}
