<?php

/*
 * Plugin Name: Kocuj Sitemap
 * Plugin URI: http://kocujsitemap.wpplugin.kocuj.pl
 * Description: This plugin adds shortcode, widget and PHP function that prepares the sitemap which contains links to all of your posts, pages, menu items, authors, tags and custom types entries in the place where it is located. It supports excluding the selected entries. It also supports multilingual websites (by using qTranslate X plugin if exists). The sitemap is automatically generated and stored in the cache to speeds up the loading of sitemap on your website.
 * Version: 2.6.5
 * Author: Dominik Kocuj
 * Author URI: http://kocuj.pl
 * License: GPL2 or newer
 * Text Domain: kocuj-sitemap
 * Domain Path: /languages/
 *
 * Kocuj Sitemap is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Kocuj Sitemap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kocuj Sitemap. If not, see http://www.gnu.org/licenses/gpl-2.0.html .
 */

/**
 * kocuj-sitemap.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespaces aliases
use KocujSitemapPlugin\Classes\Base;
use KocujSitemapPlugin\Classes\Sitemap;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

// meta translation
if (1 === 0) {
    _e('This plugin adds shortcode, widget and PHP function that prepares the sitemap which contains links to all of your posts, pages, menu items, authors, tags and custom types entries in the place where it is located. It supports excluding the selected entries. It also supports multilingual websites (by using qTranslate X plugin if exists). The sitemap is automatically generated and stored in the cache to speeds up the loading of sitemap on your website.',
        'kocuj-sitemap');
}

// initialize classes
$kocujSitemapPluginDir = dirname(__FILE__);
include $kocujSitemapPluginDir . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'base.class.php';
Base::getInstance(__FILE__);
include $kocujSitemapPluginDir . DIRECTORY_SEPARATOR . 'autoload.php';
unset($kocujSitemapPluginDir);

/**
 * Display sitemap
 *
 * @access public
 * @param string $homeLinkText
 *            Home link text in the sitemap - default: empty
 * @param string $class
 *            Sitemap class - default: empty
 * @param array $exclude
 *            List of elements to exclude - it is divided into sections: "post", "category", "author", "term" - default: empty
 * @param array $hideTypes
 *            List of elements types to hide; there are the following types allowed: "author", "custom", "home", "menu", "page", "post", "tag" - default: empty
 * @return void
 */
function kocujsitemap_show_sitemap(
    $homeLinkText = '',
    $class = '',
    array $exclude = array(),
    array $hideTypes = array()
) {
    // show sitemap
    echo Sitemap::getInstance()->get($homeLinkText, $class, $exclude, $hideTypes);
}
