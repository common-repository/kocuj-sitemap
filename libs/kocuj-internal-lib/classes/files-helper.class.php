<?php

/**
 * files-helper.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use DirectoryIterator;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Files helper class
 *
 * @access public
 */
final class FilesHelper
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
            self::$instance = new self ();
        }
        // exit
        return self::$instance;
    }

    /**
     * Check if the selected directory exists and is writable
     *
     * @access public
     * @param string $dir
     *            Directory to check
     * @return bool Directory exists and is writable (true) or not (false)
     */
    public function checkDirWritable($dir)
    {
        // exit
        return (!((!is_dir($dir)) || (!is_writable($dir))));
    }

    /**
     * Get files list from directory by reccurence
     *
     * @access private
     * @param string $rootDir
     *            Root directory
     * @param string $subDir
     *            Subdirectory
     * @param array $attr
     *            Attributes; there are the following keys available: "extlist" (array type; list of files extensions to get), "getdirs" (bool type; if true, directories will be get), "getfiles" (bool type; if true, files will be get), "ignoreitems" (array type; list of files and directories to ignore; if file or directory has to be ignored also in all subdirectories, it should begin with "*" and "/"), "sort" (bool type; if true, files are sorted), "withsubdirs" (bool type; if true, files are also get from subdirectories)
     * @return array Files and subdirectories list; each element contains the following keys: "filename" (string type; file or directory name), "isdir" (bool type: true if it is directory, false if it is file)
     */
    private function getFilesListReccurence($rootDir, $subDir, array $attr)
    {
        // set directory name
        $dir = $rootDir . DIRECTORY_SEPARATOR . $subDir;
        // check directory
        if (!is_dir($dir)) {
            return array();
        }
        // initialize
        $output = array();
        // prepare attributes
        if (!isset ($attr ['extlist'])) {
            $attr ['extlist'] = array();
        }
        if (!isset ($attr ['ignoreitems'])) {
            $attr ['ignoreitems'] = array();
        }
        if (!isset ($attr ['getfiles'])) {
            $attr ['getfiles'] = true;
        }
        if (!isset ($attr ['getdirs'])) {
            $attr ['getdirs'] = false;
        }
        if (!isset ($attr ['sort'])) {
            $attr ['sort'] = false;
        }
        if (!isset ($attr ['withsubdirs'])) {
            $attr ['withsubdirs'] = false;
        }
        // get files list from directory
        $iterator = $attr ['sort'] ? new FilesHelperSortableDirectoryIterator ($dir) : new DirectoryIterator ($dir);
        foreach ($iterator as $data) {
            if (($data->getFilename() !== '.') && ($data->getFilename() !== '..')) {
                if (((!isset ($subDir [0]) /* strlen($subDir) === 0 */) && (!in_array($data->getFilename(),
                            $attr ['ignoreitems'])) && (!in_array('*' . DIRECTORY_SEPARATOR . $data->getFilename(),
                            $attr ['ignoreitems']))) || ((isset ($subDir [0]) /* strlen($subDir) > 0 */) && (!in_array($subDir . DIRECTORY_SEPARATOR . $data->getFilename(),
                            $attr ['ignoreitems'])) && (!in_array('*' . DIRECTORY_SEPARATOR . $data->getFilename(),
                            $attr ['ignoreitems'])))) {
                    if ($data->isFile()) {
                        if (!empty ($attr ['extlist'])) {
                            $ok = false;
                            $div = explode('.', strtolower($data->getFilename()));
                            $divCount = count($div);
                            if ($divCount > 0) {
                                $ok = in_array($div [$divCount - 1], $attr ['extlist']);
                            }
                        } else {
                            $ok = true;
                        }
                        if (($ok) && ($attr ['getfiles'])) {
                            $output [] = array(
                                'filename' => $subDir . DIRECTORY_SEPARATOR . $data->getFilename(),
                                'isdir' => false
                            );
                        }
                    } else {
                        if ($data->isDir()) {
                            if ($attr ['withsubdirs']) {
                                $output = array_merge($output, $this->getFilesListReccurence($rootDir,
                                    $subDir . DIRECTORY_SEPARATOR . $data->getFilename(), $attr));
                            }
                            if ($attr ['getdirs']) {
                                $output [] = array(
                                    'filename' => $subDir . DIRECTORY_SEPARATOR . $data->getFilename(),
                                    'isdir' => true
                                );
                            }
                        }
                    }
                }
            }
        }
        // exit
        return $output;
    }

    /**
     * Get files list from directory
     *
     * @access public
     * @param string $dir
     *            Directory
     * @param array $attr
     *            Attributes; there are the following keys available: "extlist" (array type; list of files extensions to get), "getdirs" (bool type; if true, directories will be get), "getfiles" (bool type; if true, files will be get), "ignoreitems" (array type; list of files and directories to ignore; if file or directory has to be ignored also in all subdirectories, it should begin with "*" and "/"), "sort" (bool type; if true, files are sorted), "withsubdirs" (bool type; if true, files are also get from subdirectories) - default: empty
     * @return array Files and subdirectories list; each element contains the following keys: "filename" (string type; file or directory name), "isdir" (bool type: true if it is directory, false if it is file)
     */
    public function getFilesList($dir, array $attr = array())
    {
        // exit
        return $this->getFilesListReccurence($dir, '', $attr);
    }

    /**
     * Remove files from directory
     *
     * @access public
     * @param string $dir
     *            Directory
     * @param
     *            array &$output Output of files and subdirectories list; each element contains the following keys: "filename" (string type; file or directory name), "isdir" (bool type: true if it is directory, false if it is file)
     * @param array $attr
     *            Attributes; there are the following keys available: "extlist" (array type; list of files extensions to get), "getdirs" (bool type; if true, directories will be get), "getfiles" (bool type; if true, files will be get), "ignoredirerrors" (bool type; if true, errors during removing the directory will not be set), "ignoreitems" (array type; list of files and directories to ignore; if file or directory has to be ignored also in all subdirectories, it should begin with "*" and "/"), "withsubdirs" (bool type; if true, files are also get from subdirectories) - default: empty
     * @return bool Files has been removed correctly (true) or there was an error (false)
     */
    public function removeFiles($dir, &$output, array $attr = array())
    {
        // disable "sort" element from attributes
        $attr ['sort'] = false;
        // remove files
        if (is_dir($dir)) {
            // get files list from directory
            $output = $this->getFilesList($dir, $attr);
            // remove files and subdirectories from directory
            if (!empty ($output)) {
                foreach ($output as $file) {
                    if (!$file ['isdir']) {
                        $fileToRemove = $dir . DIRECTORY_SEPARATOR . $file ['filename'];
                        if (!@unlink($fileToRemove)) {
                            return false;
                        }
                    }
                }
                if ((isset ($attr ['withsubdirs'])) && ($attr ['withsubdirs'])) {
                    foreach ($output as $file) {
                        if ($file ['isdir']) {
                            $dirToRemove = $dir . DIRECTORY_SEPARATOR . $file ['filename'];
                            if ((file_exists($dirToRemove)) && (is_dir($dirToRemove))) {
                                if ((!@rmdir($dirToRemove)) && ((!isset ($attr ['ignoredirerrors'])) || ($attr ['ignoredirerrors']))) {
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if ((!isset ($attr ['ignoredirerrors'])) || (!$attr ['ignoredirerrors'])) {
                return false;
            }
        }
        // exit
        return true;
    }

    /**
     * Copy files
     *
     * @access public
     * @param string $from
     *            Directory from which files should be copied
     * @param string $to
     *            Directory to which files should be copied
     * @param
     *            array &$output Output of files and subdirectories list; each element contains the following keys: "filename" (string type; file or directory name), "isdir" (bool type: true if it is directory, false if it is file)
     * @param array $attr
     *            Attributes; there are the following keys available: "extlist" (array type; list of files extensions to get), "getdirs" (bool type; if true, directories will be get), "getfiles" (bool type; if true, files will be get), "ignoredirerrors" (bool type; if true, errors during removing the directory will not be set), "ignoreitems" (array type; list of files and directories to ignore; if file or directory has to be ignored also in all subdirectories, it should begin with "*" and "/"), "withsubdirs" (bool type; if true, files are also get from subdirectories) - default: empty
     * @return bool Files has been removed correctly (true) or there was an error (false)
     */
    public function copyFiles($from, $to, &$output, array $attr = array())
    {
        // optionally create target directory
        if (!is_dir($to)) {
            $oldMask = umask(0);
            @mkdir($to, 0755, true);
            umask($oldMask);
        }
        // copy files
        if (is_dir($from)) {
            // get files list from directory
            $output = $this->getFilesList($from, $attr);
            // copy files and subdirectories from directory
            if (!empty ($output)) {
                if ((isset ($attr ['withsubdirs'])) && ($attr ['withsubdirs'])) {
                    foreach ($output as $file) {
                        $dirToCopy = $to . DIRECTORY_SEPARATOR . $file ['filename'];
                        if (($file ['isdir']) && (!is_dir($dirToCopy))) {
                            $oldMask = umask(0);
                            if ((!@mkdir($dirToCopy, 0755,
                                    true)) && ((!isset ($attr ['ignoredirerrors'])) || ($attr ['ignoredirerrors']))) {
                                umask($oldMask);
                                return false;
                            }
                            umask($oldMask);
                        }
                    }
                }
                foreach ($output as $file) {
                    if (!$file ['isdir']) {
                        if (!@copy($from . DIRECTORY_SEPARATOR . $file ['filename'],
                            $to . DIRECTORY_SEPARATOR . $file ['filename'])) {
                            return false;
                        }
                    }
                }
            }
        } else {
            if ((!isset ($attr ['ignoredirerrors'])) || (!$attr ['ignoredirerrors'])) {
                return false;
            }
        }
        // exit
        return true;
    }

    /**
     * Create file
     *
     * @access public
     * @param string $filename
     *            Filename
     * @param string $content
     *            File content - default: empty
     * @return bool File has been created correctly (true) or there was an error (false)
     */
    public function createFile($filename, $content = '')
    {
        // save file
        $file = @fopen($filename, 'w');
        if ($file === false) {
            return false;
        }
        if (flock($file, LOCK_EX) === false) {
            @fclose($file);
            return false;
        }
        if (ftruncate($file, 0) === false) {
            flock($file, LOCK_UN);
            @fclose($file);
            return false;
        }
        if ((isset ($content [0]) /* strlen($content) > 0 */) && (fwrite($file, $content) === false)) {
            flock($file, LOCK_UN);
            @fclose($file);
            return false;
        }
        if (fflush($file) === false) {
            flock($file, LOCK_UN);
            @fclose($file);
            return false;
        }
        if (flock($file, LOCK_UN) === false) {
            @fclose($file);
            return false;
        }
        if (fclose($file) === false) {
            return false;
        }
        // exit
        return true;
    }

    /**
     * Get relative path
     *
     * @access public
     * @param string $from
     *            From path
     * @param string $to
     *            To path
     * @return string Relative path
     */
    public function getRelativePath($from, $to)
    {
        // get relative path
        $divFrom = explode(DIRECTORY_SEPARATOR, rtrim($from, DIRECTORY_SEPARATOR));
        $divTo = explode(DIRECTORY_SEPARATOR, rtrim($to, DIRECTORY_SEPARATOR));
        while ((count($divFrom)) && (count($divTo)) && (($divFrom [0] === $divTo [0]))) {
            array_shift($divFrom);
            array_shift($divTo);
        }
        // set output
        $output = str_pad('', count($divFrom) * 3, '..' . DIRECTORY_SEPARATOR) . implode(DIRECTORY_SEPARATOR, $divTo);
        if (substr($output, -1) === DIRECTORY_SEPARATOR) {
            $output = substr($output, 0, -1);
        }
        // exit
        return $output;
    }
}
