<?php

/**
 * filters-remover.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\Helpers;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Filters remover class
 *
 * @access public
 */
class FiltersRemover
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * List of filters to temporarily disable
     *
     * @access private
     * @var array
     */
    private $filters = array();

    /**
     * List of disabled filters
     *
     * @access private
     * @var array
     */
    private $filtersDisabled = array();

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Disable cloning of object
     *
     * @access private
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Get singleton instance
     *
     * @access public
     * @return object Singleton instance
     */
    public static function getInstance()
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self();
        }
        // exit
        return self::$instance;
    }

    /**
     * Set filters for temporarily disable
     *
     * @access public
     * @param string $id
     *            Identifier for set of filters
     * @param array $filters
     *            Filters list
     * @return void
     */
    public function setFilters($id, array $filters)
    {
        // set filters
        $this->filters[$id] = $filters;
    }

    /**
     * Temporarily disable selected filters
     *
     * @access public
     * @param string $id
     *            Identifier for set of filters
     * @return void
     */
    public function disableFilters($id)
    {
        // disable filters
        if (isset($this->filters[$id])) {
            foreach ($this->filters[$id] as $filter => $args) {
                $value = has_filter($filter, $args[0]);
                if ($value !== false) {
                    if (!isset($this->filtersDisabled[$id])) {
                        $this->filtersDisabled[$id] = array();
                    }
                    $this->filtersDisabled[$id][] = $filter;
                    remove_filter($filter, $args[0], $args[1]);
                }
            }
        }
    }

    /**
     * Enable temporarily disabled filters
     *
     * @access public
     * @param string $id
     *            Identifier for set of filters
     * @return void
     */
    public function enableFilters($id)
    {
        // enable filters
        if (isset($this->filtersDisabled[$id])) {
            foreach ($this->filtersDisabled[$id] as $filter) {
                if (isset($this->filters[$id][$filter])) {
                    add_filter($filter, $this->filters[$id][$filter][0], $this->filters[$id][$filter][1],
                        $this->filters[$id][$filter][2]);
                }
            }
            unset($this->filtersDisabled[$id]);
        }
    }
}
