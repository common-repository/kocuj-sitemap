<?php

/**
 * files-helper-sortable-directory-iterator.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use ArrayObject;
use DirectoryIterator;
use IteratorAggregate;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Files helper sortable directory iterator class
 *
 * @access public
 */
final class FilesHelperSortableDirectoryIterator implements IteratorAggregate
{

    /**
     * Storage
     *
     * @access private
     * @var object
     */
    private $_storage;

    /**
     * Constructor
     *
     * @access public
     * @param string $path
     *            Path to directory
     * @return void
     */
    public function __construct($path)
    {
        // initialize
        $this->_storage = new ArrayObject ();
        // get files
        $files = new DirectoryIterator ($path);
        foreach ($files as $file) {
            $this->_storage->offsetSet($file->getFilename(), $file->getFileInfo());
        }
        // sort files
        $this->_storage->uksort(function ($a, $b) {
            // exit
            return strcmp($a, $b);
        });
    }

    /**
     * Get iterator
     *
     * @access public
     * @return object Iterator
     */
    public function getIterator()
    {
        // exit
        return $this->_storage->getIterator();
    }
}
