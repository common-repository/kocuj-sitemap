<?php

/**
 * container-type.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Enums\Project\Components\All\Options;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Containers types constants class
 *
 * @access public
 */
final class ContainerType
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
     * Network or site container
     */
    const NETWORK_OR_SITE = 0;

    /**
     * Site container
     */
    const SITE = 1;

    /**
     * Network container
     */
    const NETWORK = 2;

    /**
     * Widget container
     */
    const WIDGET = 3;

    /**
     * Data set site container
     */
    const DATA_SET_SITE = 4;

    /**
     * Data set network container
     */
    const DATA_SET_NETWORK = 5;
}
