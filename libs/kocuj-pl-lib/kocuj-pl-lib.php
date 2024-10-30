<?php

/**
 * kocuj-pl-lib.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib\kocuj_pl_lib
 */

// set namespace
namespace KocujPlLib\V13a;

// set namespaces aliases
use KocujPlLib\V13a\Classes\LibDirs;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

// load library if do not exists already
if (!class_exists('\\KocujPlLib\\V13a\\Classes\\Project', false)) {
    // check if Kocuj Internal Lib exists and if it is administration panel
    if ((class_exists('\\KocujIL\\V13a\\Classes\\Project')) && ((is_admin()) || (is_network_admin()))) {
        // initialize directories
        include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'lib-dirs.class.php';
        LibDirs::getInstance();

        /**
         * Automatic loading of classes
         *
         * @access public
         * @param string $class
         *            Class name
         * @return void
         */
        function autoload($class)
        {
            // load class
            \KocujIL\V13a\autoload($class, 'KocujPlLib', 'V13a',
                LibDirs::getInstance()->getMain());
        }

        // set automatic loading of classes
        spl_autoload_register('\\KocujPlLib\\V13a\\autoload');
    }
}
