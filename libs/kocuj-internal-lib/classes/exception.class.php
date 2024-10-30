<?php

/**
 * exception.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a\Enums\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Exception class
 *
 * @access public
 */
class Exception extends \Exception
{

    /**
     * Namespace prefix
     *
     * @access protected
     * @var string
     */
    protected $namespacePrefix = 'KocujIL\\V13a';

    /**
     * Errors data
     *
     * @access protected
     * @var array
     */
    protected $errors = array();

    /**
     * Optional argument for error message
     *
     * @access private
     * @var string
     */
    private $param;

    /**
     * Constructor
     *
     * @access public
     * @param object $componentObj
     *            Component object; if empty, it will not be used in exception codes and message
     * @param int $code
     *            Error code
     * @param string $file
     *            Filename where there was an error; should be set to __FILE__ during throwing an exception
     * @param int $line
     *            Line number where there was an error; should be set to __LINE__ during throwing an exception
     * @param string $param
     *            Optional argument for error message - default: empty
     * @return void
     */
    public function __construct($componentObj, $code, $file, $line, $param = '')
    {
        // initialize errors
        $this->setErrors();
        // get component name
        $componentCategory = -1;
        $componentCategoryString = 'unknown';
        $componentType = '';
        if ($componentObj !== null) {
            $componentClass = get_class($componentObj);
            if ((substr($componentClass, 0,
                        strlen($this->namespacePrefix)) === $this->namespacePrefix) && (is_subclass_of($componentObj,
                    $this->namespacePrefix . '\\Classes\\ComponentObject'))) {
                $div = explode('\\', $componentClass);
                $divCount = count($div);
                if (($divCount >= 6) && ($div [$divCount - 6] === 'Classes') && ($div [$divCount - 5] === 'Project') && ($div [$divCount - 4] === 'Components') && ($div [$divCount - 1] === 'Component')) {
                    $componentCategoryString = strtolower(substr(preg_replace('/([A-Z])/', '-$1', $div [$divCount - 3]),
                        1));
                    switch ($componentCategoryString) {
                        case 'all' :
                            $componentCategory = ProjectCategory::ALL;
                            break;
                        case 'frontend' :
                            $componentCategory = ProjectCategory::FRONTEND;
                            break;
                        case 'backend' :
                            $componentCategory = ProjectCategory::BACKEND;
                            break;
                        default :
                            $componentCategory = -1;
                    }
                    $componentType = strtolower(substr(preg_replace('/([A-Z])/', '-$1', $div [$divCount - 2]), 1));
                }
            }
        }
        // save parameters
        $this->param = $param;
        // prepare message
        $errors = ($componentCategory !== -1) ? $componentObj->getProjectObj()->getComponentErrors($componentCategory,
            $componentType) : $this->errors;
        $message = '[' . $this->getExceptionName() . '] [';
        if ($componentCategory !== -1) {
            $message .= 'component: ' . $componentCategoryString . '/' . $componentType . ', ';
        }
        $message .= 'code: ' . $code . ', file: ' . $file . ', line: ' . $line . '] ' . ((isset ($errors [$code])) ? $errors [$code] : 'Unknown error');
        if (isset ($param [0]) /* strlen($param) > 0 */) {
            $message .= ' (' . $param . ')';
        }
        // execute parent constructor
        parent::__construct($message, $code);
        // save parent parameters
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * Get errors data
     *
     * @access protected
     * @return string Exception name
     */
    protected function getExceptionName()
    {
        // exit
        return Helper::getInstance()->getPrefix();
    }

    /**
     * Set errors data
     *
     * @access protected
     * @return void
     */
    protected function setErrors()
    {
        // initialize errors
        $this->errors = array(
            ExceptionCode::OK => 'OK',
            ExceptionCode::NO_REQUIRED_SETTING_DATA => 'No required setting data',
            ExceptionCode::NO_REQUIRED_LIBRARY => 'No required library',
            ExceptionCode::NO_REQUIRED_COMPONENT => 'No required component',
            ExceptionCode::CANNOT_USE_COMPONENTS_AFTER_SHUTDOWN => 'Cannot use components after shutdown'
        );
    }

    /**
     * Get errors
     *
     * @access protected
     * @return array Errors
     */
    protected function getErrors()
    {
        // exit
        return $this->errors;
    }

    /**
     * Get optional argument for error message
     *
     * @access public
     * @return string Optional argument for error message
     */
    public function getParam()
    {
        // exit
        return $this->param;
    }
}
