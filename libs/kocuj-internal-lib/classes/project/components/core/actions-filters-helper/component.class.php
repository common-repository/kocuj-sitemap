<?php

/**
 * component.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\Core\ActionsFiltersHelper;

// set namespace
use KocujIL\V13a\Classes\ComponentObject;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Actions and filters helper class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Callbacks data
     *
     * @access private
     * @var array
     */
    private $callbacks = array();

    /**
     * Call method
     *
     * @access public
     * @param string $method
     *            Method to call
     * @param array $arguments
     *            Arguments
     * @return bool|float|int|object|string Returned value
     */
    public function __call($method, array $arguments)
    {
        // check if it is callback
        $div = explode('_', $method);
        if ((count($div) === 2) && ($div [0] === 'callback') && (is_numeric($div [1])) && (isset ($this->callbacks [$div [1]]))) {
            // get component object
            $componentObj = $this->getComponent($this->callbacks [$div [1]] ['component'],
                $this->callbacks [$div [1]] ['category'], $this->callbacks [$div [1]] ['fragment']);
            // execute callback
            if ($componentObj !== null) {
                return call_user_func_array(array(
                    $componentObj,
                    $this->callbacks [$div [1]] ['method']
                ), $arguments);
            }
        }
        // exit
        return null;
    }

    /**
     * Add action or filter callback from component which will be initialized only when needed
     *
     * @access private
     * @param string $actionOrFilter
     *            Action or filter hook
     * @param int $callbackComponentProjectCategory
     *            Callback component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category)
     * @param string $callbackComponent
     *            Callback component
     * @param string $callbackComponentFragment
     *            Callback component fragment
     * @param string $callbackMethod
     *            Callback method
     * @param int $prior
     *            Action or filter callback priority
     * @param int $argumentsCount
     *            Action or filter callback arguments count
     * @param bool $isFilter
     *            It is filter (true) or action (false)
     * @return void
     */
    private function addActionOrFilterWhenNeeded(
        $actionOrFilter,
        $callbackComponentProjectCategory,
        $callbackComponent,
        $callbackComponentFragment,
        $callbackMethod,
        $prior,
        $argumentsCount,
        $isFilter
    ) {
        // add action or filter callback
        $id = count($this->callbacks);
        $callback = 'callback_' . $id;
        $this->callbacks [$id] = array(
            'category' => $callbackComponentProjectCategory,
            'component' => $callbackComponent,
            'fragment' => $callbackComponentFragment,
            'method' => $callbackMethod
        );
        // add action or filter
        if ($isFilter) {
            add_filter($actionOrFilter, array(
                $this,
                $callback
            ), $prior, $argumentsCount);
        } else {
            add_action($actionOrFilter, array(
                $this,
                $callback
            ), $prior, $argumentsCount);
        }
    }

    /**
     * Add action callback from component which will be initialized only when needed
     *
     * @access public
     * @param string $action
     *            Action hook
     * @param int $callbackComponentProjectCategory
     *            Callback component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category)
     * @param string $callbackComponent
     *            Callback component
     * @param string $callbackComponentFragment
     *            Callback component fragment
     * @param string $callbackMethod
     *            Callback method
     * @param int $prior
     *            Action callback priority - default: 10
     * @param int $argumentsCount
     *            Action callback arguments count - default: 1
     * @return void
     */
    public function addActionWhenNeeded(
        $action,
        $callbackComponentProjectCategory,
        $callbackComponent,
        $callbackComponentFragment,
        $callbackMethod,
        $prior = 10,
        $argumentsCount = 1
    ) {
        // add action
        $this->addActionOrFilterWhenNeeded($action, $callbackComponentProjectCategory, $callbackComponent,
            $callbackComponentFragment, $callbackMethod, $prior, $argumentsCount, false);
    }

    /**
     * Add filter callback from component which will be initialized only when needed
     *
     * @access public
     * @param string $filter
     *            Filter hook
     * @param int $callbackComponentProjectCategory
     *            Callback component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category)
     * @param string $callbackComponent
     *            Callback component
     * @param string $callbackComponentFragment
     *            Callback component fragment
     * @param string $callbackMethod
     *            Callback method
     * @param int $prior
     *            Filter callback priority - default: 10
     * @param int $argumentsCount
     *            Filter callback arguments count - default: 1
     * @return void
     */
    public function addFilterWhenNeeded(
        $filter,
        $callbackComponentProjectCategory,
        $callbackComponent,
        $callbackComponentFragment,
        $callbackMethod,
        $prior = 10,
        $argumentsCount = 1
    ) {
        // add filter
        $this->addActionOrFilterWhenNeeded($filter, $callbackComponentProjectCategory, $callbackComponent,
            $callbackComponentFragment, $callbackMethod, $prior, $argumentsCount, true);
    }
}
