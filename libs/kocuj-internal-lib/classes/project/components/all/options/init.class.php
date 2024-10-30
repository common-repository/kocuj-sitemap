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
namespace KocujIL\V13a\Classes\Project\Components\All\Options;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Enums\Project\Components\All\Options\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Options (component initialization) class
 *
 * @access public
 */
class Init extends ComponentInitObject
{

    /**
     * Constructor
     *
     * @access public
     * @param object $projectObj
     *            \KocujIL\V13a\Classes\Project object for current project
     * @return void
     */
    public function __construct($projectObj)
    {
        // execute parent
        parent::__construct($projectObj);
        // set errors
        $this->errors = array(
            ExceptionCode::TYPE_ID_EXISTS => 'Options type identifier already exists',
            ExceptionCode::TYPE_ID_DOES_NOT_EXIST => 'Options type identifier does not exist',
            ExceptionCode::CONTAINER_ID_EXISTS => 'Options container identifier already exists',
            ExceptionCode::CONTAINER_ID_DOES_NOT_EXIST => 'Options container identifier does not exist',
            ExceptionCode::DEFINITION_ID_EXISTS => 'Option definition identifier already exists',
            ExceptionCode::DEFINITION_ID_DOES_NOT_EXIST => 'Option definition identifier does not exist',
            ExceptionCode::WRONG_CONTAINER_TYPE_FOR_THIS_METHOD => 'Wrong container type for use with this method',
            ExceptionCode::CANNOT_USE_ARRAY_OPTION_IN_WIDGET => 'Cannot use an array option in widget settings',
            ExceptionCode::CANNOT_USE_OPTION_AS_SEARCH_KEY => 'Cannot use option as search key'
        );
    }
}
