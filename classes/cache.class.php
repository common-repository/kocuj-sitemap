<?php

/**
 * cache.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujSitemapPlugin\Enums\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Plugin cache class
 *
 * @access public
 */
class Cache
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Cache content
     *
     * @access private
     * @var array
     */
    private $cacheContent = array();

    /**
     * Cache has been created now (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $cacheCreated = false;

    /**
     * Constructor
     *
     * @access private
     * @param array $actions
     *            Cache actions - default: empty
     * @param array $filters
     *            Cache filters - default: empty
     * @return void
     */
    private function __construct(array $actions = array(), array $filters = array())
    {
        // add actions and filters
        if ((is_admin()) || (is_network_admin())) {
            add_action('permalink_structure_changed', array(
                $this,
                'actionRecreateCache'
            ), KocujIL\Classes\Helper::getInstance()->calculateMaxPriority('permalink_structure_changed'));
            $parsCount = (is_multisite()) ? 2 : 1;
            add_action('activated_plugin', array(
                $this,
                'actionClearCacheForPlugin'
            ), KocujIL\Classes\Helper::getInstance()->calculateMaxPriority('activated_plugin'), $parsCount);
            add_action('deactivated_plugin', array(
                $this,
                'actionClearCacheForPlugin'
            ), KocujIL\Classes\Helper::getInstance()->calculateMaxPriority('deactivated_plugin'), $parsCount);
            $this->addRecreateCacheFiltersOrActions($filters, true);
            $this->addRecreateCacheFiltersOrActions($actions, false);
            if (is_multisite()) {
                add_action('delete_blog', array(
                    $this,
                    'actionDeleteBlog'
                ), KocujIL\Classes\Helper::getInstance()->calculateMaxPriority('delete_blog'));
            }
        }
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
     * @param array $actions
     *            Cache actions - default: empty
     * @param array $filters
     *            Cache filters - default: empty
     * @return object Singleton instance
     */
    public static function getInstance(array $actions = array(), array $filters = array())
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self($actions, $filters);
        }
        // exit
        return self::$instance;
    }

    /**
     * Get cache root directory
     *
     * @access public
     * @return string Cache root directory
     */
    public function getCacheRootDirectory()
    {
        // get cache root directory
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'kocuj-sitemap';
    }

    /**
     * Add filters or actions for recreating cache
     *
     * @access private
     * @param array $data
     *            Filters or actions data
     * @param bool $isFilter
     *            It is filter (true) or action (false)
     * @return void
     */
    private function addRecreateCacheFiltersOrActions(array $data, $isFilter)
    {
        // add filters or actions
        if (!empty($data)) {
            $added = array();
            foreach ($data as $element) {
                if (!in_array($element, $added)) {
                    if ($isFilter) {
                        add_filter($element, array(
                            $this,
                            'filterRecreateCache'
                        ), KocujIL\Classes\Helper::getInstance()->calculateMaxPriority($element));
                    } else {
                        add_action($element, array(
                            $this,
                            'actionRecreateCache'
                        ), KocujIL\Classes\Helper::getInstance()->calculateMaxPriority($element));
                    }
                    $added[] = $element;
                }
            }
        }
    }

    /**
     * Check if cache directory is writable
     *
     * @access public
     * @return bool Cache directory is writable (true) or not (false)
     */
    public function checkWritable()
    {
        // check if directory exists
        if (!is_dir($this->getCacheDirectory())) {
            $oldMask = umask(0);
            @mkdir($this->getCacheDirectory(), 0755, true);
            umask($oldMask);
        }
        // exit
        return (!((!KocujIL\Classes\FilesHelper::getInstance()->checkDirWritable($this->getCacheRootDirectory())) || (!KocujIL\Classes\FilesHelper::getInstance()->checkDirWritable($this->getCacheDirectory()))));
    }

    /**
     * Get cache directory
     *
     * @access public
     * @param int $siteId
     *            Site identifier; if it is set to 0, cache directory will be get for current site; this is ignored if it is not multisite installation - default: 0
     * @return string Cache directory
     */
    public function getCacheDirectory($siteId = 0)
    {
        // get cache directory
        return $this->getCacheRootDirectory() . DIRECTORY_SEPARATOR . ((is_multisite()) ? ($siteId === 0 ? get_current_blog_id() : $siteId) : 1);
    }

    /**
     * Get cache filename
     *
     * @access private
     * @param string $locale
     *            Language locale
     * @return string Cache filename - if empty, there is an error in cache directory
     * @throws Exception
     */
    private function getCacheFilename($locale)
    {
        // get cache directory
        $dir = $this->getCacheDirectory();
        // get random value
        $securityFilename = $dir . DIRECTORY_SEPARATOR . 'security.cache.php';
        if (!is_file($securityFilename)) {
            // check if directory is writable
            if (!$this->checkWritable()) {
                throw new Exception(ExceptionCode::CACHE_DIRECTORY_IS_NOT_WRITABLE, __FILE__, __LINE__,
                    $this->getCacheDirectory());
            }
            // save security file
            $content = '<' . '?php' . PHP_EOL;
            $content .= '// this is the random value for secure cache files - it has to be secret, so do not tell it to anyone; you can change it by typing new code manually or by removing this file, if you think that this value is not secret and is known to other people' . PHP_EOL;
            $content .= '$securityValue = \'' . KocujIL\Classes\Helper::getInstance()->getBetterRandom(111111111,
                    999999999) . '\';' . PHP_EOL;
            if (!KocujIL\Classes\FilesHelper::getInstance()->createFile($securityFilename, $content)) {
                throw new Exception(ExceptionCode::UNABLE_TO_WRITE_TO_CACHE_SECURITY_FILE, __FILE__, __LINE__,
                    $securityFilename);
            }
        }
        $securityValue = '';
        if (!is_file($securityFilename)) {
            throw new Exception(ExceptionCode::UNABLE_TO_READ_FROM_CACHE_SECURITY_FILE, __FILE__, __LINE__,
                $securityFilename);
        }
        include $securityFilename;
        if (!isset($securityValue[0]) /* strlen($securityValue) === 0 */) {
            throw new Exception(ExceptionCode::WRONG_DATA_IN_CACHE_SECURITY_FILE, __FILE__, __LINE__,
                $securityFilename);
        }
        // exit
        return $dir . DIRECTORY_SEPARATOR . 'cache_' . $locale . '_' . $securityValue . '.cache';
    }

    /**
     * Clear or purge cache
     *
     * @access private
     * @param bool $purgeCache
     *            Clear all cache files and directories (true) or clear cache files for current site only (false)
     * @param int $siteId
     *            Site identifier; if it is set to 0, cache will be cleared for current site or purged for all sites; this is ignored if it is not multisite installation - default: 0
     * @return void
     * @throws Exception
     */
    private function clearOrPurgeCache($purgeCache, $siteId = 0)
    {
        // remove all cache files
        $filesList = array();
        if (!$purgeCache) {
            $cacheDirectory = $this->getCacheDirectory($siteId);
            if (!KocujIL\Classes\FilesHelper::getInstance()->removeFiles($cacheDirectory, $filesList, array(
                'extlist' => array(
                    'cache'
                )
            ))) {
                throw new Exception(ExceptionCode::UNABLE_TO_CLEAR_CACHE, __FILE__, __LINE__, $cacheDirectory);
            }
        } else {
            $cacheDirectory = ($siteId === 0) ? $this->getCacheRootDirectory() : $this->getCacheDirectory($siteId);
            if (!KocujIL\Classes\FilesHelper::getInstance()->removeFiles($cacheDirectory, $filesList, array(
                'withsubdirs' => true,
                'getdirs' => true
            ))) {
                throw new Exception(ExceptionCode::UNABLE_TO_CLEAR_CACHE, __FILE__, __LINE__, $cacheDirectory);
            }
            if (!@rmdir($cacheDirectory)) {
                throw new Exception(ExceptionCode::UNABLE_TO_CLEAR_CACHE, __FILE__, __LINE__, $cacheDirectory);
            }
        }
        // remove cache content from memory
        $this->cacheContent = array();
        $this->cacheCreated = false;
    }

    /**
     * Clear cache
     *
     * @access public
     * @param int $siteId
     *            Site identifier; if it is set to 0, cache will be cleared for current site; this is ignored if it is not multisite installation - default: 0
     * @return void
     * @throws Exception
     */
    public function clearCache($siteId = 0)
    {
        // clear cache
        $this->clearOrPurgeCache(false, $siteId);
    }

    /**
     * Purge cache
     *
     * @access public
     * @param int $siteId
     *            Site identifier; if it is set to 0, cache will be purged for all sites; this is ignored if it is not multisite installation - default: 0
     * @return void
     * @throws Exception
     */
    public function purgeCache($siteId = 0)
    {
        // purge cache
        $this->clearOrPurgeCache(true, $siteId);
    }

    /**
     * Create cache
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function createCache()
    {
        // check if cache has not been created now and if it is activated
        if (($this->cacheCreated) || (Options::getInstance()->getOption('Cache') === '0')) {
            return;
        }
        // clear cache
        $this->clearCache();
        // check if cache should be generated
        $cacheFrontend = Options::getInstance()->getOption('CacheFrontend');
        if ((($cacheFrontend === '1') && (!is_admin()) && (!is_network_admin())) || ($cacheFrontend === '0')) {
            // get all languages
            $langs = MultipleLanguages::getInstance()->getLanguages();
            // if cache is generated for frontend, it will be generated only for current locale
            if ($cacheFrontend) {
                $locale = get_locale();
                if (!in_array($locale, $langs)) {
                    $locale = 'en_US';
                }
                $langs = array(
                    $locale
                );
            }
            // create cached sitemap for each language
            $this->cacheContent = array();
            foreach ($langs as $lang) {
                // set sitemap data
                $this->cacheContent[$lang] = array(
                    'dt' => Sitemap::getInstance()->create($lang),
                    'vr' => 3
                );
                // check if cache directory is writable
                if (!$this->checkWritable()) {
                    throw new Exception(ExceptionCode::CACHE_DIRECTORY_IS_NOT_WRITABLE,
                        __FILE__, __LINE__, $this->getCacheDirectory());
                }
                // save "index.html" file for secure directory
                if (!KocujIL\Classes\FilesHelper::getInstance()->createFile($this->getCacheDirectory() . DIRECTORY_SEPARATOR . 'index.html',
                    '')) {
                    throw new Exception(ExceptionCode::UNABLE_TO_WRITE_TO_CACHE_INDEX_HTML_FILE, __FILE__, __LINE__);
                }
                // save cache
                if (!KocujIL\Classes\FilesHelper::getInstance()->createFile($this->getCacheFilename($lang),
                    maybe_serialize($this->cacheContent[$lang]))) {
                    @unlink($this->getCacheFilename($lang));
                    throw new Exception(ExceptionCode::UNABLE_TO_WRITE_TO_CACHE_FILE, __FILE__, __LINE__);
                }
                // set cache as created
                $this->cacheCreated = true;
            }
        }
    }

    /**
     * Load cache
     *
     * @access public
     * @return string Returned buffer
     * @throws Exception
     */
    public function loadCache()
    {
        // load cache
        try {
            // get locale
            $locale = get_locale();
            // check if cache is activated
            $cache = Options::getInstance()->getOption('Cache');
            if ($cache === '1') {
                // optionally get cache from memory
                if (!empty($this->cacheContent)) {
                    $array = (isset($this->cacheContent[$locale])) ? $this->cacheContent[$locale] : array();
                } else {
                    // set filename
                    $locales = array(
                        $locale,
                        $locale,
                        'en_US',
                        'en_US'
                    );
                    $localesCount = count($locales);
                    $filename = '';
                    for ($z = 0; $z < $localesCount; $z++) {
                        $filename = $this->getCacheFilename($locales[$z]);
                        if (is_file($filename)) {
                            break;
                        } else {
                            if ($z === $localesCount - 1) {
                                throw new Exception(ExceptionCode::UNABLE_TO_READ_FROM_CACHE_FILE, __FILE__, __LINE__,
                                    $filename);
                            } else {
                                $this->clearCache();
                                $this->createCache();
                            }
                        }
                    }
                    // load file
                    $input = @file_get_contents($filename);
                    if ($input === false) {
                        throw new Exception(ExceptionCode::UNABLE_TO_READ_FROM_CACHE_FILE, __FILE__, __LINE__,
                            $filename);
                    }
                    $array = maybe_unserialize($input);
                    // check cache version
                    if ((!isset($array['vr'])) || ($array['vr'] !== 3)) {
                        // recreate cache
                        $this->clearCache();
                        $this->createCache();
                        // load file
                        $input = @file_get_contents($filename);
                        if ($input === false) {
                            throw new Exception(ExceptionCode::UNABLE_TO_READ_FROM_CACHE_FILE,
                                __FILE__, __LINE__, $filename);
                        }
                        $array = maybe_unserialize($input);
                    }
                }
            } else {
                $array = array(
                    'dt' => Sitemap::getInstance()->create($locale)
                );
            }
            // set output buffer
            if (!isset($array['dt'])) {
                throw new Exception(ExceptionCode::UNABLE_TO_READ_FROM_CACHE_FILE, __FILE__, __LINE__, $filename);
            }
        } catch (\Exception $e) {
            $this->clearCache();
            throw new Exception($e->getCode(), $e->getFile(), $e->getLine(), $e->getParam());
        }
        // exit
        return $array['dt'];
    }

    /**
     * Action - recreate cache
     *
     * @access public
     * @return void
     */
    public function actionRecreateCache()
    {
        // renew cache
        try {
            $this->createCache();
        } catch (\Exception $e) {
        }
    }

    /**
     * Filter - recreate cache
     *
     * @access public
     * @param array|bool|float|int|object|string $data
     *            Filter data
     * @return array|bool|float|int|object|string Filter data
     */
    public function filterRecreateCache($data)
    {
        // renew cache
        $this->actionRecreateCache();
        // exit
        return $data;
    }

    /**
     * Action - clear cache for plugin which was activated or deactivated
     *
     * @access public
     * @param string $plugin
     *            Plugin name
     * @param bool $networkWide
     *            Plugin is enabled or disabled for the entire network (true) or for site only (false) - default: false
     * @return void
     */
    public function actionClearCacheForPlugin($plugin, $networkWide = false)
    {
        // check if plugin is supported
        $pluginsFiles = MultipleLanguagesData::getInstance()->getPluginsFiles();
        if (in_array($plugin, $pluginsFiles)) {
            // clear cache
            try {
                $this->clearOrPurgeCache($networkWide);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Action - purge cache for deleting site
     *
     * @access public
     * @param int $siteId
     *            Site identifier
     * @return void
     */
    public function actionDeleteBlog($siteId)
    {
        // purge cache for removed site
        try {
            $this->purgeCache($siteId);
        } catch (\Exception $e) {
        }
    }
}
