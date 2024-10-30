<?php

/**
 * kocujsitemap-plugin-langs.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

// load required class
if (!class_exists('_WP_Editors')) {
    include ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-wp-editor.php';
}

// for IDE
if (1 === 0) {

    class _WP_Editors
    {
    }
}

/**
 * TinyMCE editor texts
 *
 * @var string
 */
/* translators: %s: Name of this plugin ("Kocuj Sitemap") */
$strings = 'tinyMCE.addI18n("' . _WP_Editors::$mce_locale . '.kocujsitemap", ' . json_encode(array(
        'longname' => sprintf(__('%s WordPress plugin buttons', 'kocuj-sitemap'), 'Kocuj Sitemap'),
        'buttontitle' => __('Add sitemap shortcode', 'kocuj-sitemap')
    )) . ');';
