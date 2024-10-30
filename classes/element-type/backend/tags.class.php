<?php

/**
 * tags.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\ElementType\Backend;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujSitemapPlugin\Classes\ElementTypeBackendParent;
use KocujSitemapPlugin\Classes\Helpers\Sort;
use KocujSitemapPlugin\Classes\Helpers\Url;
use KocujSitemapPlugin\Classes\MultipleLanguages;
use KocujSitemapPlugin\Classes\Options;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Tags type administration panel class
 *
 * @access public
 */
class Tags extends ElementTypeBackendParent
{

    /**
     * Temporary locale
     *
     * @access private
     * @var string Temporary locale
     */
    private $localeTemp = '';

    /**
     * Temporary parameters
     *
     * @access private
     * @var array Temporary parameters
     */
    private $parsTemp = array();

    /**
     * Filter for generate tags cloud
     *
     * @access public
     * @param string $data
     *            Tags cloud
     * @param array $tags
     *            Tags data
     * @return array Tags cloud
     */
    public function filterWPGenerateTagCloud($data, array $tags)
    {
        // initialize
        $output = array();
        // execute after get tags callback
        MultipleLanguages::getInstance()->doSomething('aftergettags', $this->localeTemp);
        // replace tags
        if (!empty($tags)) {
            // prepare tags index by names
            $tagsIndex = array();
            foreach ($tags as $key => $tag) {
                $tagsIndex[$tag->name] = $key;
            }
            // translate tags in cloud
            $loopCount = count($tags);
            for ($z = 0; $z < $loopCount; $z++) {
                // initialize
                $fullLink = $data[$z];
                // get name
                $div = explode('>', $fullLink);
                if (count($div) > 1) {
                    $div2 = explode('<', $div[1]);
                    if (count($div2) > 0) {
                        $name = $div2[0];
                        // get tag data by name
                        if (isset($tagsIndex[$name])) {
                            // initialize
                            $additionalClass = array();
                            // get tag data
                            $id = $tags[$tagsIndex[$name]]->term_id;
                            $link = $tags[$tagsIndex[$name]]->link;
                            MultipleLanguages::getInstance()->doSomething('beforegettagitem',
                                $this->localeTemp, $id);
                            $div2[0] = apply_filters('kocujsitemap_link_text',
                                MultipleLanguages::getInstance()->getItem('tagname',
                                    $this->localeTemp, $name, $id), $id, 'tag', $this->localeTemp);
                            $div[1] = implode('<', $div2);
                            $fullLink = implode('>', $div);
                            unset($div, $div2);
                            // translate URL
                            $pos = stripos($fullLink, 'href=');
                            $containerClassPos = $pos - 1;
                            if (($pos !== false) && (isset($fullLink[$pos + 5]) /* strlen($fullLink) > $pos+5 */)) {
                                $quote = $fullLink[$pos + 5];
                                $div = explode($quote, $fullLink);
                                $pos = array_search($link, $div);
                                if (($pos !== false) && ($pos >= 1)) {
                                    $div[$pos] = esc_url(Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('tagurl',
                                        $this->localeTemp, get_tag_link($id), $id)));
                                }
                                $oldPos = $pos;
                                $pos = array_search(strtolower(' class='), array_map('strtolower', $div));
                                if (($pos !== false) && ($pos > $oldPos) && ($pos + 1 < count($div))) {
                                    $additionalClass[] = $div[$pos + 1];
                                    unset($div[$pos], $div[$pos + 1]);
                                }
                                $fullLink = implode($quote, $div);
                                unset($div);
                            }
                            // remember data
                            $fullLink = apply_filters('kocujsitemap_element', $fullLink, $id, 'tag', $this->localeTemp);
                            $output[] = array(
                                'id' => $id,
                                'tp' => 'term',
                                'lk' => $fullLink,
                                'cp' => $containerClassPos,
                                'sc' => true,
                                'ac' => $additionalClass
                            );
                            MultipleLanguages::getInstance()->doSomething('aftergettagitem',
                                $this->localeTemp, $id);
                        }
                    }
                }
            }
            // sort data
            if ($this->parsTemp['orderby'] === 'name') {
                // get tags data
                $tagsData = $this->getTagsData($this->parsTemp['orderby'], $this->parsTemp['order'],
                    $this->parsTemp['number'], false, $this->localeTemp);
                // sort data
                if (!empty($output)) {
                    $tagsTemp = array();
                    foreach ($tagsData as $key => $val) {
                        foreach ($output as $val2) {
                            if ($val['id'] === $val2['id']) {
                                $tagsTemp[$key] = $val2;
                                break;
                            }
                        }
                    }
                    $output = array_values($tagsTemp);
                }
            }
        }
        // exit
        return $output;
    }

    /**
     * Get tags data
     *
     * @access private
     * @param string $sortColumn
     *            Sort column - default: name
     * @param string $sortOrder
     *            Sort order - default: asc
     * @param int $number
     *            Number of tags; 0 means all - default: all
     * @param bool $onlyUsed
     *            Only used tags (true) or not (false) - default: false
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    private function getTagsData($sortColumn = 'name', $sortOrder = 'asc', $number = 0, $onlyUsed = false, $locale = '')
    {
        // initialize
        $output = array();
        // get tags data
        MultipleLanguages::getInstance()->doSomething('beforegettags', $locale);
        $tags = get_tags(array(
            'orderby' => $sortColumn,
            'order' => $sortOrder,
            'hide_empty' => $onlyUsed,
            'number' => ($number === 0) ? '' : $number,
            'offset' => 0
        ));
        MultipleLanguages::getInstance()->doSomething('aftergettags', $locale);
        foreach ($tags as $tag) {
            MultipleLanguages::getInstance()->doSomething('beforegettagitem', $locale,
                $tag->term_id);
            $linkText = apply_filters('kocujsitemap_link_text',
                MultipleLanguages::getInstance()->getItem('tagname', $locale, $tag->name,
                    $tag->term_id), $tag->term_id, 'tag', $locale);
            if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                $linkText = '-';
            }
            $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('tagurl',
                $locale, get_tag_link($tag->term_id), $tag->term_id));
            $link = apply_filters('kocujsitemap_element',
                KocujIL\Classes\HtmlHelper::getInstance()->getLink($url, $linkText), $tag->term_id, 'tag', $locale);
            $outputAdd = array(
                'id' => $tag->term_id,
                'tp' => 'term',
                'lk' => $link,
                'ur' => $url
            );
            if ($sortColumn === 'name') {
                $outputAdd['sortname'] = $linkText;
            }
            $output[] = $outputAdd;
            MultipleLanguages::getInstance()->doSomething('aftergettagitem', $locale,
                $tag->term_id);
        }
        if ($sortColumn === 'name') {
            $output = Sort::getInstance()->sortElements($output, $sortOrder);
        }
        // exit
        return $output;
    }

    /**
     * Add tags to sitemap
     *
     * @access public
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    public function getElementArray($locale = '')
    {
        // initialize
        $array = array();
        // add tags to sitemap
        $value = Options::getInstance()->getOption('DisplayTags');
        if ($value === '1') {
            // get tags
            $value = Options::getInstance()->getOption('DisplayTagsSort');
            $sortColumn = 'name';
            if ((isset($value[0]) /* strlen($value) > 0 */) && ($value !== 'name')) {
                $sortColumns = array(
                    'posts' => 'count'
                );
                $sortColumn = $sortColumns[$value];
            }
            $value = Options::getInstance()->getOption('DisplayTagsOrder');
            $sortOrder = 'ASC';
            if (isset($value[0]) /* strlen($value) > 0 */) {
                switch ($value) {
                    case 'desc':
                        $sortOrder = 'DESC';
                        break;
                }
            }
            $isCloud = Options::getInstance()->getOption('DisplayTagsCloud');
            if ($isCloud === '1') {
                $prior = KocujIL\Classes\Helper::getInstance()->calculateMaxPriority('wp_generate_tag_cloud');
                add_filter('wp_generate_tag_cloud', array(
                    $this,
                    'filterWPGenerateTagCloud'
                ), $prior, 2);
                $pars = array(
                    'separator' => ' ',
                    'number' => Options::getInstance()->getOption('DisplayTagsCloudNumber'),
                    'orderby' => $sortColumn,
                    'order' => $sortOrder
                );
                $pars = apply_filters('kocujsitemap_tagscloudparameters', $pars, $locale);
                $pars['link'] = 'view';
                $pars['format'] = 'array';
                $pars['echo'] = 0;
                MultipleLanguages::getInstance()->doSomething('beforegettags', $locale);
                $this->localeTemp = $locale;
                $this->parsTemp = $pars;
                $cloud = wp_tag_cloud($pars);
                $this->localeTemp = '';
                $this->parsTemp = array();
                remove_filter('wp_generate_tag_cloud', array(
                    $this,
                    'filterWPGenerateTagCloud'
                ), $prior);
                if (!empty($cloud)) {
                    $loopCount = count($cloud);
                    for ($z = 0; $z < $loopCount; $z++) {
                        $tag = $cloud[$z];
                        $arrayAdd = array(
                            'id' => $tag['id'],
                            'tp' => $tag['tp'],
                            'cp' => $tag['cp'],
                            'sc' => true,
                            'lk' => $tag['lk']
                        );
                        if ($z < $loopCount - 1) {
                            $arrayAdd['lk'] .= $pars['separator'];
                        }
                        if (isset($tag['ch'])) {
                            $arrayAdd['ch'] = $tag['ch'];
                        }
                        if (isset($tag['ac'])) {
                            $arrayAdd['ac'] = $tag['ac'];
                        }
                        $array[] = $arrayAdd;
                    }
                }
            } else {
                $onlyUsed = Options::getInstance()->getOption('DisplayTagsUsed');
                $array = $this->getTagsData($sortColumn, $sortOrder, 0, $onlyUsed === '1', $locale);
            }
        }
        // exit
        return $array;
    }

    /**
     * Get administration page settings
     *
     * @access public
     * @return array Administration page settings
     */
    public function getAdminPageSettings()
    {
        // exit
        return array(
            'title' => __('Tags list options', 'kocuj-sitemap'),
            'id' => 'tags_options',
            'help' => array(
                'overview' => array(
                    'title' => __('Overview', 'kocuj-sitemap'),
                    'content' => __('This is the place where you can enable or disable displaying of tags in the sitemap. You can also change how they will be displayed.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into three tabs: `displaying tags`, `options` and `section title`. Each tab can be selected by clicking on it.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save tags list options`.',
                            'kocuj-sitemap')
                ),
                'displaying_tags' => array(
                    'title' => __('`Displaying tags` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display tags',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be tags list displayed in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'options' => array(
                    'title' => __('`Options` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display only used tags',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be displayed only tags used in at least one post. This option has no effect if option `display tags as cloud` is checked.',
                            'kocuj-sitemap') . '<li><em>`' . __('Display tags as cloud',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, tags will be displayed as cloud instead of list. This option has effect only for tags used by at least one post.',
                            'kocuj-sitemap') . '<li><em>`' . __('Number of tags to display in cloud',
                            'kocuj-sitemap') . '`</em>: ' . __('You can specify the number of tags that will be displayed in the tags cloud. If you enter here the number greater or equal the quantity of tags, then all tags will be displayed. Enter here `0` to display all tags in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Sort tags by',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort tags in the sitemap using the selected tags properties. There are the following tags properties to select:',
                            'kocuj-sitemap') . '<ul>' . '<li><em>`' . __('name',
                            'kocuj-sitemap') . '`</em>: ' . __('Tags will be sorted by name.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('posts count',
                            'kocuj-sitemap') . '`</em>: ' . __('Tags will be sorted by posts count.',
                            'kocuj-sitemap') . '</li>' . '</ul>' . '</li>' . '<li><em>`' . __('Sort tags order',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort tags by ascending or descending order.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'section_title' => array(
                    'title' => __('`Section title` tab', 'kocuj-sitemap'),
                    'content' => __('In this tab you can set the title of the section with tags. It is used if option `divide display into sections` in the main settings of the Kocuj Sitemap plugin is checked.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('There are fields to enter the section title for each language that is available in your WordPress installation. If you have not activated any of the supported plugins for multilingualism, there will be visible only two fields to enter the title - for current WordPress language and for default section title in English language.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('It is not necessary to enter section titles here. However, the place is here in order to be able to display the title of the section in your chosen language if there is no translation for it. If you leave this empty and if you will not have a translation of section title for current language and for default language (English language), there will be displayed the standard section title in English language.',
                            'kocuj-sitemap')
                )
            )
        );
    }

    /**
     * Get administration page data
     *
     * @access public
     * @return array Administration page data
     */
    public function getAdminPageData()
    {
        // exit
        return array(
            'tabs' => array(
                'tags_displaying' => array(
                    'title' => __('Displaying tags', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayTags',
                            __('If this option is checked, there will be tags list displayed in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                ),
                'tags_options' => array(
                    'title' => __('Options', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayTagsUsed',
                            __('If this option is checked, there will be displayed only tags used in at least one post.',
                                'kocuj-sitemap'),
                            array(),
                            array(
                                'global_addinfo' => __('If tags are set to display as the cloud, this option has no effect.',
                                    'kocuj-sitemap')
                            )
                        ),
                        array(
                            'checkbox',
                            'DisplayTagsCloud',
                            __('If this option is checked, tags will be displayed as cloud instead of list.',
                                'kocuj-sitemap'),
                            array(),
                            array(
                                'global_addinfo' => __('Only used tags will be shown as the cloud.', 'kocuj-sitemap')
                            )
                        ),
                        array(
                            'text',
                            'DisplayTagsCloudNumber',
                            __('You can specify the number of tags that will be displayed in the tags cloud. Enter here `0` to display all tags in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array(
                                'global_addinfo' => __('Value `0` means all tags.', 'kocuj-sitemap')
                            )
                        ),
                        array(
                            'select',
                            'DisplayTagsSort',
                            __('You can sort tags in the sitemap using the selected tags properties.', 'kocuj-sitemap'),
                            array(),
                            array(
                                'options' => array(
                                    'name' => __('name', 'kocuj-sitemap'),
                                    'posts' => __('posts count', 'kocuj-sitemap')
                                )
                            )
                        ),
                        array(
                            'select',
                            'DisplayTagsOrder',
                            __('You can sort tags by ascending or descending order.', 'kocuj-sitemap'),
                            array(),
                            array(
                                'options' => array(
                                    'asc' => __('ascending', 'kocuj-sitemap'),
                                    'desc' => __('descending', 'kocuj-sitemap')
                                )
                            )
                        )
                    )
                )
            ),
            'submit' => array(
                'label' => __('Save tags list options', 'kocuj-sitemap'),
                'tooltip' => __('Save current tags list options', 'kocuj-sitemap')
            )
        );
    }

    /**
     * Get administration panel order name
     *
     * @access public
     * @return string Administration panel order name
     */
    public function getAdminOrderName()
    {
        // exit
        return __('Tags', 'kocuj-sitemap');
    }

    /**
     * Get administration cache actions
     *
     * @access public
     * @return array Administration cache actions
     */
    public function getAdminCacheActions()
    {
        // exit
        return array(
            'created_term',
            'delete_term',
            'edited_term'
        );
    }
}
