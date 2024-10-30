<?php

/**
 * some-plugin.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\MultipleLanguages;

// set namespaces aliases
use KocujSitemapPlugin\Interfaces\Language;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Multi-language plugin class
 *
 * @access public
 */
class SomePlugin implements Language
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

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
            self::$instance = new SomePlugin();
        }
        // exit
        return self::$instance;
    }

    /**
     * Get translation plugin name; it is used in administration panel during the selection of multi-lingual plugin
     *
     * @access public
     * @return string Plugin name
     */
    public function getName()
    {
        // get plugin name
        return 'Some plugin';
    }

    /**
     * Get plugin priority; it is used to sort translation plugins usage order; the lowest number is the first plugin in order, the highest number is the last plugin in order
     *
     * @access public
     * @return string Plugin priority
     */
    public function getPriority()
    {
        // get plugin priority
        return 10;
    }

    /**
     * Get languages list
     *
     * @access public
     * @return array Languages list
     */
    public function getLanguages()
    {
        // exit
        return array();
    }

    /**
     * Get full URL to language flag
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return string URL to language flag
     */
    public function getLanguageFlag($locale)
    {
        // exit
        return '';
    }

    /**
     * Check if translation plugin exists; this method checks if all functions, classes, methods, properties and global variables, which are used from translation plugin, exists
     *
     * @access public
     * @return bool Translation plugin exists (true) or not (false)
     */
    public function checkPlugin()
    {
        // exit
        return true;
    }

    /**
     * Get translation plugin filename with its directory
     *
     * @access public
     * @return string Translation plugin filename
     */
    public function getPluginFile()
    {
        // get plugin name
        return 'someplugin/someplugin.php';
    }

    /**
     * Something to do before processing
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeProcess($locale)
    {
    }

    /**
     * Something to do after processing
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterProcess($locale)
    {
    }

    /**
     * Something to do before get blog name
     *
     * @access public
     * @return void
     */
    public function beforeGetBlogName()
    {
    }

    /**
     * Something to do after get blog name
     *
     * @access public
     * @return void
     */
    public function afterGetBlogName()
    {
    }

    /**
     * Get translated blog name
     *
     * @access public
     * @param string $origText
     *            Original text
     * @param string $locale
     *            Language locale
     * @return string Translated text
     */
    public function getTranslatedBlogName($origText, $locale)
    {
        // get translated blog name
        return $origText;
    }

    /**
     * Something to do before get home URL
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetHomeURL($locale)
    {
    }

    /**
     * Something to do after get home URL
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetHomeURL($locale)
    {
    }

    /**
     * Get translated home URL
     *
     * @access public
     * @param string $origURL
     *            Original home URL
     * @param string $locale
     *            Language locale
     * @return string Translated home URL
     */
    public function getTranslatedHomeURL($origURL, $locale)
    {
        // get translated home URL
        return $origURL;
    }

    /**
     * Something to do before get pages
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPages($locale)
    {
    }

    /**
     * Something to do after get pages
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPages($locale)
    {
    }

    /**
     * Something to do before get page item
     *
     * @access public
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPageItem($pageId, $locale)
    {
    }

    /**
     * Something to do after get page item
     *
     * @access public
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPageItem($pageId, $locale)
    {
    }

    /**
     * Get translated page URL
     *
     * @access public
     * @param string $origURL
     *            Original page URL
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return string Translated page URL
     */
    public function getTranslatedPageURL($origURL, $pageId, $locale)
    {
        // get translated page URL
        return $origURL;
    }

    /**
     * Get translated page title
     *
     * @access public
     * @param string $origTitle
     *            Original page title
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return string Translated page title
     */
    public function getTranslatedPageTitle($origTitle, $pageId, $locale)
    {
        // get translated page title
        return $origTitle;
    }

    /**
     * Something to do before get posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPosts($locale)
    {
    }

    /**
     * Something to do after get posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPosts($locale)
    {
    }

    /**
     * Something to do before get post item
     *
     * @access public
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPostItem($postId, $locale)
    {
    }

    /**
     * Something to do after get post item
     *
     * @access public
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPostItem($postId, $locale)
    {
    }

    /**
     * Get translated post URL
     *
     * @access public
     * @param string $origURL
     *            Original post URL
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return string Translated post URL
     */
    public function getTranslatedPostURL($origURL, $postId, $locale)
    {
        // get translated post URL
        return $origURL;
    }

    /**
     * Get translated post title
     *
     * @access public
     * @param string $origTitle
     *            Original post title
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return string Translated post title
     */
    public function getTranslatedPostTitle($origTitle, $postId, $locale)
    {
        // get translated post title
        return $origTitle;
    }

    /**
     * Something to do before get categories
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCategories($locale)
    {
    }

    /**
     * Something to do after get categories
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCategories($locale)
    {
    }

    /**
     * Something to do before get category item
     *
     * @access public
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCategoryItem($categoryId, $locale)
    {
    }

    /**
     * Something to do after get category item
     *
     * @access public
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCategoryItem($categoryId, $locale)
    {
    }

    /**
     * Get translated category URL
     *
     * @access public
     * @param string $origURL
     *            Original category URL
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return string Translated category URL
     */
    public function getTranslatedCategoryURL($origURL, $categoryId, $locale)
    {
        // get translated category URL
        return $origURL;
    }

    /**
     * Get translated category title
     *
     * @access public
     * @param string $origTitle
     *            Original category title
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return string Translated category title
     */
    public function getTranslatedCategoryTitle($origTitle, $categoryId, $locale)
    {
        // get translated category title
        return $origTitle;
    }

    /**
     * Something to do before get taxonomies
     *
     * @access public
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTaxonomies($taxonomy, $locale)
    {
    }

    /**
     * Something to do after get taxonomies
     *
     * @access public
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTaxonomies($taxonomy, $locale)
    {
    }

    /**
     * Something to do before get taxonomy item
     *
     * @access public
     * @param int $termId
     *            Term id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTaxonomyItem($termId, $taxonomy, $locale)
    {
    }

    /**
     * Something to do after get taxonomy item
     *
     * @access public
     * @param int $termId
     *            Taxonomy id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTaxonomyItem($termId, $taxonomy, $locale)
    {
    }

    /**
     * Get translated taxonomy URL
     *
     * @access public
     * @param string $origURL
     *            Original taxonomy URL
     * @param int $termId
     *            Taxonomy id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return string Translated taxonomy URL
     */
    public function getTranslatedTaxonomyURL($origURL, $termId, $taxonomy, $locale)
    {
        // get translated taxonomy URL
        return $origURL;
    }

    /**
     * Get translated taxonomy title
     *
     * @access public
     * @param string $origTitle
     *            Original taxonomy title
     * @param int $termId
     *            Taxonomy id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return string Translated taxonomy title
     */
    public function getTranslatedTaxonomyTitle($origTitle, $termId, $taxonomy, $locale)
    {
        // get translated taxonomy title
        return $origTitle;
    }

    /**
     * Something to do before get menu
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetMenu($locale)
    {
    }

    /**
     * Something to do after get menu
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetMenu($locale)
    {
    }

    /**
     * Something to do before get menu item
     *
     * @access public
     * @param int $itemId
     *            Menu item id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetMenuItem($itemId, $locale)
    {
    }

    /**
     * Something to do after get menu item
     *
     * @access public
     * @param int $itemId
     *            Menu item id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetMenuItem($itemId, $locale)
    {
    }

    /**
     * Get translated menu URL
     *
     * @access public
     * @param string $origURL
     *            Original menu URL
     * @param int $menuId
     *            Menu id
     * @param string $locale
     *            Language locale
     * @return string Translated menu URL
     */
    public function getTranslatedMenuURL($origURL, $menuId, $locale)
    {
        // get translated menu URL
        return $origURL;
    }

    /**
     * Get translated menu title
     *
     * @access public
     * @param string $origTitle
     *            Original menu title
     * @param int $menuId
     *            Menu id
     * @param string $locale
     *            Language locale
     * @return string Translated menu title
     */
    public function getTranslatedMenuTitle($origTitle, $menuId, $locale)
    {
        // get translated menu title
        return $origTitle;
    }

    /**
     * Something to do before get authors
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetAuthors($locale)
    {
    }

    /**
     * Something to do after get authors
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetAuthors($locale)
    {
    }

    /**
     * Something to do before get author item
     *
     * @access public
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetAuthorItem($authorId, $locale)
    {
    }

    /**
     * Something to do after get author item
     *
     * @access public
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetAuthorItem($authorId, $locale)
    {
    }

    /**
     * Get translated author URL
     *
     * @access public
     * @param string $origURL
     *            Original author URL
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return string Translated author URL
     */
    public function getTranslatedAuthorURL($origURL, $authorId, $locale)
    {
        // get translated author URL
        return $origURL;
    }

    /**
     * Get translated author name
     *
     * @access public
     * @param string $origName
     *            Original author name
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return string Translated author name
     */
    public function getTranslatedAuthorName($origName, $authorId, $locale)
    {
        // get translated author name
        return $origName;
    }

    /**
     * Something to do before get tags
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTags($locale)
    {
    }

    /**
     * Something to do after get tags
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTags($locale)
    {
    }

    /**
     * Something to do before get tag item
     *
     * @access public
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTagItem($tagId, $locale)
    {
    }

    /**
     * Something to do after get tag item
     *
     * @access public
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTagItem($tagId, $locale)
    {
    }

    /**
     * Get translated tag URL
     *
     * @access public
     * @param string $origURL
     *            Original tag URL
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return string Translated tag URL
     */
    public function getTranslatedTagURL($origURL, $tagId, $locale)
    {
        // get translated tag URL
        return $origURL;
    }

    /**
     * Get translated tag name
     *
     * @access public
     * @param string $origName
     *            Original tag name
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return string Translated tag name
     */
    public function getTranslatedTagName($origName, $tagId, $locale)
    {
        // get translated tag name
        return $origName;
    }

    /**
     * Something to do before get custom posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCustomPosts($locale)
    {
    }

    /**
     * Something to do after get custom posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCustomPosts($locale)
    {
    }

    /**
     * Something to do before get custom post item
     *
     * @access public
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCustomPostItem($customPostId, $locale)
    {
    }

    /**
     * Something to do after get custom post item
     *
     * @access public
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCustomPostItem($customPostId, $locale)
    {
    }

    /**
     * Get translated custom post URL
     *
     * @access public
     * @param string $origURL
     *            Original custom post URL
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return string Translated custom post URL
     */
    public function getTranslatedCustomPostURL($origURL, $customPostId, $locale)
    {
        // get translated custom post URL
        return $origURL;
    }

    /**
     * Get translated custom post title
     *
     * @access public
     * @param string $origTitle
     *            Original custom post title
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return string Translated custom post title
     */
    public function getTranslatedCustomPostTitle($origTitle, $customPostId, $locale)
    {
        // get translated custom post title
        return $origTitle;
    }
}
