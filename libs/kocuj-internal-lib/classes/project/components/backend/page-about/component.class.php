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
namespace KocujIL\V13a\Classes\Project\Components\Backend\PageAbout;

// set namespace
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\HtmlHelper;
use KocujIL\V13a\Classes\LibUrls;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * About page class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Image filename
     *
     * @access private
     * @var string
     */
    private $imageFilename = '';

    /**
     * Additional information
     *
     * @access private
     * @var string
     */
    private $additionalInfo = '';

    /**
     * Set image filename
     *
     * @access public
     * @param string $imageFilename
     *            Image filename
     * @return void
     */
    public function setImageFilename($imageFilename)
    {
        // set value
        $this->imageFilename = $imageFilename;
    }

    /**
     * Get image filename
     *
     * @access public
     * @return string Image filename
     */
    public function getImageFilename()
    {
        // exit
        return $this->imageFilename;
    }

    /**
     * Set additional information
     *
     * @access public
     * @param string $additionalInfo
     *            Additional information
     * @return void
     */
    public function setAdditionalInfo($additionalInfo)
    {
        // set value
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * Get additional information
     *
     * @access public
     * @return string Additional information
     */
    public function getAdditionalInfo()
    {
        // exit
        return $this->additionalInfo;
    }

    /**
     * Show links
     *
     * @access private
     * @param array $links
     *            Links data; each link data has the following fields: "external" (bool type; if true, link will be open in new window), "link" (string type; link URL); there are available the following optional fields: "image" (string type; image filename without directory and PNG extension), "name" (string type; link name; if empty, link URL will be used as link name)
     * @param bool $inline
     *            Links will be displayed in one line without text (true) or in multiple lines with text (false)
     * @return string Links to display
     */
    private function showLinks(array $links, $inline = false)
    {
        // initialize
        $output = '';
        // set links
        foreach ($links as $linkData) {
            if (isset ($linkData ['link'] [0]) /* strlen($linkData['link']) > 0 */) {
                $imageTag = ((isset ($linkData ['image'])) && (isset ($linkData ['image'] [0]) /* strlen($linkData['image']) > 0 */)) ? HtmlHelper::getInstance()->getImage(LibUrls::getInstance()->get('images') . '/project/components/backend/page-about/' . $linkData ['image'] . '.png',
                    array(
                        'style' => 'float:left;margin-right:4px;' . ($inline ? '' : 'margin-bottom:4px;')
                    )) : '';
                if ($inline) {
                    $output .= HtmlHelper::getInstance()->getLink($linkData ['link'], $imageTag,
                        array(
                            'external' => $linkData ['external']
                        ));
                } else {
                    $output .= $imageTag . '&nbsp;' . HtmlHelper::getInstance()->getLink($linkData ['link'],
                            ((isset ($linkData ['name'])) ? $linkData ['name'] : ''), array(
                                'external' => $linkData ['external']
                            ));
                }
                if (!$inline) {
                    $output .= '<div style="clear:both;"></div>';
                }
            }
        }
        // set line ending
        if ($inline) {
            $output .= '<div style="clear:both;"></div>';
        }
        // exit
        return ((isset ($output [0]) /* strlen($output) > 0 */)) ? $output . '<br />' : '';
    }

    /**
     * Show page with information about plugin or theme
     *
     * @access public
     * @return void
     */
    public function showPage()
    {
        // show begin div
        echo '<div' . $this->getComponent('project-helper')->applyFiltersForHTMLStyleAndClass('page_about_div') . ' id="' . $this->getComponent('project-helper')->getPrefix() . '__div_page_about">';
        $this->getComponent('project-helper')->doAction('before_page_about');
        // optionally show image
        if (isset ($this->imageFilename [0]) /* strlen($this->imageFilename) > 0 */) {
            // check if file exists
            $filename = $this->getComponent('dirs')->getSubDir('customimages') . DIRECTORY_SEPARATOR . $this->imageFilename;
            if (is_file($filename)) {
                // show image
                echo HtmlHelper::getInstance()->getImage($this->getComponent('urls')->get('customimages') . '/' . $this->imageFilename,
                    array(
                        'style' => 'width:100%;max-width:800px;height:auto;'
                    ));
                echo '<br />';
            }
        }
        // show title
        $version = $this->getProjectObj()->getMainSettingVersion();
        echo '<strong>' . $this->getProjectObj()->getMainSettingTitleOriginal() . ((isset ($version [0]) /* strlen($version) > 0 */) ? ' v.' . $version : '') . '</strong>';
        echo '<br />';
        // show project URL-s
        $url = $this->getProjectObj()->getMainSettingUrl();
        if (isset ($url [0]) /* strlen($url) > 0 */) {
            echo HtmlHelper::getInstance()->getLink($url, '', array(
                'external' => true
            ));
            echo '<br />';
        }
        // show empty line
        echo '<br />';
        // show links
        echo $this->showLinks(array(
            array(
                'link' => $this->getProjectObj()->getSettingArray('newschannels', 'rss'),
                'external' => true,
                'image' => 'rss'
            ),
            array(
                'link' => $this->getProjectObj()->getSettingArray('newschannels', 'facebook'),
                'external' => true,
                'image' => 'facebook'
            ),
            array(
                'link' => $this->getProjectObj()->getSettingArray('newschannels', 'twitter'),
                'external' => true,
                'image' => 'twitter'
            )
        ));
        // show author information
        $author = $this->getProjectObj()->getSettingArray('author', 'name');
        if (isset ($author [0]) /* strlen($author) > 0 */) {
            echo $this->getStrings('page-about',
                    ProjectCategory::BACKEND)->getString('SHOW_PAGE_AUTHOR') . ': ' . $author;
            $authorUrl = $this->getProjectObj()->getSettingArray('author', 'url');
            if (isset ($authorUrl [0]) /* strlen($authorUrl) > 0 */) {
                echo ' (' . HtmlHelper::getInstance()->getLink($authorUrl, '', array(
                        'external' => true
                    )) . ')';
            }
            $authorEmail = $this->getProjectObj()->getSettingArray('author', 'email');
            if (isset ($authorEmail [0]) /* strlen($authorEmail) > 0 */) {
                echo ' &lt;' . HtmlHelper::getInstance()->getMailLink($authorEmail) . '&gt;';
            }
            echo '<br />';
        }
        // show license information
        $licenseName = $this->getProjectObj()->getMainSettingLicenseName();
        if (isset ($licenseName [0]) /* strlen($licenseName) > 0 */) {
            echo $this->getStrings('page-about',
                    ProjectCategory::BACKEND)->getString('SHOW_PAGE_LICENSE') . ': ' . $this->getComponent('license',
                    ProjectCategory::BACKEND)->getLicenseLink();
            echo '<br /><br />';
        }
        // show icons license information
        printf($this->getStrings('page-about',
            ProjectCategory::BACKEND)->getString('SHOW_PAGE_LICENSE_ICONS'),
            HtmlHelper::getInstance()->getLink('http://useiconic.com/open/', '', array(
                'external' => true
            )), HtmlHelper::getInstance()->getLink('https://opensource.org/licenses/MIT/', 'MIT',
                array(
                    'external' => true
                )),
            HtmlHelper::getInstance()->getLink('http://freebiesbooth.com/32px-social-media-icons',
                '', array(
                    'external' => true
                )));
        echo '<br /><br />';
        // show additional information
        if (isset ($this->additionalInfo [0]) /* strlen($this->additionalInfo) > 0 */) {
            echo $this->additionalInfo;
            echo '<br /><br />';
        }
        // show some links
        $repository = $this->getProjectObj()->getSettingArray('repository', 'main');
        $support = $this->getProjectObj()->getSettingArray('repository', 'support');
        $translation = $this->getProjectObj()->getSettingArray('repository', 'translation');
        if ((isset ($repository [0]) /* strlen($repository) > 0 */) || (isset ($support [0]) /* strlen($support) > 0 */) || (isset ($translation [0]) /* strlen($translation) > 0 */)) {
            echo '<hr />';
            echo '<br />';
        }
        // show wordpress.org repository information
        if (isset ($repository [0]) /* strlen($repository) > 0 */) {
            echo $this->getStrings('page-about',
                    ProjectCategory::BACKEND)->getString('SHOW_PAGE_REPOSITORY') . ':';
            echo '<br />';
            echo HtmlHelper::getInstance()->getLink($repository, '', array(
                'external' => true
            ));
            echo '<br /><br />';
        }
        // show information about support
        if (isset ($support [0]) /* strlen($support) > 0 */) {
            echo $this->getStrings('page-about',
                    ProjectCategory::BACKEND)->getString('SHOW_PAGE_SUPPORT') . ':';
            echo '<br />';
            echo HtmlHelper::getInstance()->getLink($support, '', array(
                'external' => true
            ));
            echo '<br /><br />';
        }
        // show information about helping in translation
        if (isset ($translation [0]) /* strlen($translation) > 0 */) {
            echo $this->getStrings('page-about',
                    ProjectCategory::BACKEND)->getString($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN ? 'SHOW_PAGE_TRANSLATION_PLUGIN' : 'SHOW_PAGE_TRANSLATION_THEME') . ':';
            echo '<br />';
            echo HtmlHelper::getInstance()->getLink($translation, '', array(
                'external' => true
            ));
            echo '<br /><br />';
        }
        // show information about telling others about this plugin or theme
        $url = $this->getProjectObj()->getMainSettingUrl();
        if (isset ($url [0]) /* strlen($url) > 0 */) {
            $tellFacebookUrl = $this->getProjectObj()->getSettingArray('tellothers', 'facebook');
            $tellTwitterUrl = $this->getProjectObj()->getSettingArray('tellothers', 'twitter');
            if ((isset ($tellFacebookUrl [0]) /* strlen($tellFacebookUrl) > 0 */) || (isset ($tellTwitterUrl [0]) /* strlen($tellTwitterUrl) > 0 */)) {
                echo '<hr /><br /><strong>';
                if ($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) {
                    echo $this->getStrings('page-about',
                        ProjectCategory::BACKEND)->getString('SHOW_PAGE_TELL_OTHERS_PLUGIN');
                }
                if ($this->getProjectObj()->getMainSettingType() === ProjectType::THEME) {
                    echo $this->getStrings('page-about',
                        ProjectCategory::BACKEND)->getString('SHOW_PAGE_TELL_OTHERS_THEME');
                }
                echo '</strong><br />';
                echo $this->showLinks(array(
                    array(
                        'link' => $tellFacebookUrl,
                        'external' => true,
                        'image' => 'facebook'
                    ),
                    array(
                        'link' => $tellTwitterUrl,
                        'external' => true,
                        'image' => 'twitter'
                    )
                ), true);
            }
        }
        // show end div
        $this->getComponent('project-helper')->doAction('after_page_about');
        echo '</div>';
    }
}
