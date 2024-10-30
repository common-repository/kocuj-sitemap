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
namespace KocujIL\V13a\Classes\Project\Components\Core\ProjectHelper;

// set namespace
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Helper;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Project helper class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Get prefix with project internal name for some names in library
     *
     * @access public
     * @return string Prefix
     */
    public function getPrefix()
    {
        // exit
        return Helper::getInstance()->getPrefix() . '_' . $this->getProjectObj()->getMainSettingInternalName();
    }

    /**
     * Get filter or action name
     *
     * @access private
     * @param string $filterOrActionName
     *            Filter or action name
     * @param string $filterOrActionSuffix
     *            Filter suffix - default: empty
     * @return string Filter or action name
     */
    private function getFilterOrActionName($filterOrActionName, $filterOrActionSuffix = '')
    {
        // exit
        return $this->getPrefix() . '__' . $filterOrActionName . ((isset ($filterOrActionSuffix [0]) /* strlen($filterOrActionSuffix) > 0 */) ? '_' : '') . $filterOrActionSuffix;
    }

    /**
     * Apply filters
     *
     * @access public
     * @param string $filterName
     *            Filter name
     * @param string $additionalFilterName
     *            Additional filter name - default: empty
     * @param string $filterSuffix
     *            Filter suffix - default: empty
     * @param array|bool|float|int|string $value
     *            Default value for filter - default: empty string
     * @return string Output for filters
     */
    public function applyFilters($filterName, $additionalFilterName = '', $filterSuffix = '', $value = '')
    {
        // apply filters
        $value = apply_filters($this->getFilterOrActionName($filterName, $filterSuffix), $value);
        if (isset ($additionalFilterName [0]) /* strlen($additionalFilterName) > 0 */) {
            $value = apply_filters($this->getFilterOrActionName($additionalFilterName, $filterSuffix), $value);
        }
        // exit
        return $value;
    }

    /**
     * Do actions
     *
     * @access public
     * @param string $actionName
     *            Action name
     * @param string $additionalActionName
     *            Additional action name - default: empty
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "actionsuffix" (string type; suffix for actions) - default: empty
     * @return void
     */
    public function doAction($actionName, $additionalActionName = '', array $attr = array())
    {
        // do actions
        do_action($this->getFilterOrActionName($actionName,
            (isset ($attr ['actionsuffix'])) ? $attr ['actionsuffix'] : ''));
        if (isset ($additionalActionName [0]) /* strlen($additionalActionName) > 0 */) {
            do_action($this->getFilterOrActionName($additionalActionName,
                (isset ($attr ['actionsuffix'])) ? $attr ['actionsuffix'] : ''));
        }
    }

    /**
     * Apply filters for HTML style and class
     *
     * @access public
     * @param string $filterName
     *            Filter name; it will be used for applying filters "kocujilv13a_PROJECT_INTERNAL_NAME_$filterName_class" (CSS class) and "kocujilv13a_PROJECT_INTERNAL_NAME_$filterName_style" (CSS style)
     * @param string $additionalFilterName
     *            Additional filter name; it will be used for applying additional filters "kocujilv13a_PROJECT_INTERNAL_NAME_$additionalFilterName_class" (CSS class) and "kocujilv13a_PROJECT_INTERNAL_NAME_$additionalFilterName_style" (CSS style) - default: empty
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "defaultclass" (string type; default value for class filter), "defaultstyle" (string type; default value for style filter)
     * @return string Output for filters
     */
    public function applyFiltersForHTMLStyleAndClass($filterName, $additionalFilterName = '', array $attr = array())
    {
        // apply filters
        $values = array(
            'style' => 'defaultstyle',
            'class' => 'defaultclass'
        );
        $output = array();
        foreach ($values as $key => $val) {
            $value = $this->applyFilters($filterName, $additionalFilterName, $key,
                (isset ($attr [$val])) ? $attr [$val] : '');
            if (isset ($value [0]) /* strlen($value) > 0 */) {
                $output [] = ' ' . $key . '="' . esc_attr($value) . '"';
            }
        }
        // exit
        return implode('', $output);
    }
}
