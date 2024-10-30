<?php

/**
 * init.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\Backend\PageAbout;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Page about (component initialization) class
 *
 * @access public
 */
class Init extends ComponentInitObject
{

    /**
     * Required components
     *
     * @access protected
     * @var array
     */
    protected $requiredComponents = array(
        '' => array(
            ProjectCategory::BACKEND => array(
                'license'
            )
        )
    );
}
