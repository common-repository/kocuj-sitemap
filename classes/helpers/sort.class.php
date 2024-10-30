<?php

/**
 * sort.class.php
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
 * Sorting class
 *
 * @access public
 */
class Sort
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

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
     * Sort elements
     *
     * @access public
     * @param array $data
     *            Data to sort
     * @param string $sortOrder
     *            Sort order: can be "asc" or "desc"
     * @return array Sorted data
     */
    public function sortElements(array $data, $sortOrder)
    {
        // sort elements
        if (!empty($data)) {
            // sort elements
            switch (strtolower($sortOrder)) {
                case 'asc':
                    usort($data, function ($a, $b) {
                        return strcasecmp($a['sortname'], $b['sortname']);
                    });
                    break;
                case 'desc':
                    usort($data, function ($a, $b) {
                        return -strcasecmp($a['sortname'], $b['sortname']);
                    });
                    break;
            }
            // remove unneeded element
            foreach ($data as $key => $val) {
                unset($data[$key]['sortname']);
            }
        }
        // exit
        return $data;
    }
}
