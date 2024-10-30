<?php

/**
 * autoload.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin;

// set namespaces aliases
use KocujSitemapPlugin\Classes\Base;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

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
    if (substr($class, 0, 19) === 'KocujSitemapPlugin\\') {
        $div = explode('\\', $class);
        $count = count($div);
        $filename = $div[$count - 1];
        unset($div[$count - 1], $div[0]);
        $dirArray = preg_replace('/([A-Z])/', '-$1', $div);
        array_walk($dirArray, function (&$item) {
            $item = substr($item, 1);
        });
        include(Base::getInstance()->getPluginDir() . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR,
                $dirArray)) . DIRECTORY_SEPARATOR . strtolower(substr(preg_replace('/([A-Z])/', '-$1', $filename),
                1)) . '.class.php');
    }
}

// set automatic loading of classes
spl_autoload_register('\\KocujSitemapPlugin\\autoload');
