<?php

/**
 * parent-type.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Menu parent types constants class
 *
 * @access public
 */
final class ParentType
{

    /**
     * Empty constructor for blocking of creating an instance of this class
     *
     * @access private
     * @var void
     */
    private function __construct()
    {
    }

    /**
     * Dashboard menu
     */
    const DASHBOARD = 0;

    /**
     * Posts menu
     */
    const POSTS = 1;

    /**
     * Media menu
     */
    const MEDIA = 2;

    /**
     * Links menu
     */
    const LINKS = 3;

    /**
     * Pages menu
     */
    const PAGES = 4;

    /**
     * Comments menu
     */
    const COMMENTS = 5;

    /**
     * Themes menu
     */
    const THEMES = 6;

    /**
     * Plugins menu
     */
    const PLUGINS = 7;

    /**
     * Users menu
     */
    const USERS = 8;

    /**
     * Tools menu
     */
    const TOOLS = 9;

    /**
     * Options menu
     */
    const OPTIONS = 10;

    /**
     * Network: dashboard menu
     */
    const NETWORK_DASHBOARD = 11;

    /**
     * Network: sites menu
     */
    const NETWORK_SITES = 12;

    /**
     * Network: users menu
     */
    const NETWORK_USERS = 13;

    /**
     * Network: themes menu
     */
    const NETWORK_THEMES = 14;

    /**
     * Network: plugins menu
     */
    const NETWORK_PLUGINS = 15;

    /**
     * Network: options menu
     */
    const NETWORK_OPTIONS = 16;
}
