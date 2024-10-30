<?php

/**
 * kocuj-internal-lib.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a;

// set namespaces aliases
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Classes\LibDirs;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

// load library if do not exists already
if (!class_exists('\\KocujIL\\V13a\\Classes\\Project', false)) {
    // initialize directories
    include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'lib-dirs.class.php';
    LibDirs::getInstance();

    /**
     * Automatic loading of classes
     *
     * @access public
     * @param string $class
     *            Class name
     * @param string $requiredPrefix1
     *            Required class prefix; if empty "KocujIL" will be used - default: empty
     * @param string $requiredPrefix2
     *            Required class prefix; if empty "V13a" will be used - default: empty
     * @param string $mainDir
     *            Main directory with classes; if empty, main directory for Kocuj Internal Lib will be used - default: empty
     * @return void
     */
    function autoload($class, $requiredPrefix1 = '', $requiredPrefix2 = '', $mainDir = '')
    {
        // initialize
        if (!isset ($requiredPrefix1 [0]) /* strlen($requiredPrefix1) === 0 */) {
            $requiredPrefix1 = 'KocujIL';
        }
        if (!isset ($requiredPrefix2 [0]) /* strlen($requiredPrefix2) === 0 */) {
            $requiredPrefix2 = 'V13a';
        }
        if (!isset ($mainDir [0]) /* strlen($mainDir) === 0 */) {
            $mainDir = LibDirs::getInstance()->getMain();
        }
        // load class
        $requiredPrefix = $requiredPrefix1 . '\\' . $requiredPrefix2 . '\\';
        if (substr($class, 0, strlen($requiredPrefix)) === $requiredPrefix) {
            $div = explode('\\', $class);
            $count = count($div);
            $filename = $div [$count - 1];
            unset ($div [$count - 1], $div [1], $div [0]);
            $dirArray = preg_replace('/([A-Z])/', '-$1', $div);
            array_walk($dirArray, function (&$item) {
                $item = substr($item, 1);
            });
            include($mainDir . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR,
                    $dirArray)) . DIRECTORY_SEPARATOR . strtolower(substr(preg_replace('/([A-Z])/', '-$1', $filename),
                    1)) . '.class.php');
        }
    }

    // set automatic loading of classes
    spl_autoload_register('\\KocujIL\\V13a\\autoload');

    // initialize required classes
    JsHelper::getInstance();
}
