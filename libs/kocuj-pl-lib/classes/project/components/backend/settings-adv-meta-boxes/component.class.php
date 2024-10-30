<?php

/**
 * component.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib\kocuj_pl_lib
 */

// set namespace
namespace KocujPlLib\V13a\Classes\Project\Components\Backend\SettingsAdvMetaBoxes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujPlLib\V13a\Classes\Helper;
use KocujPlLib\V13a\Classes\LibUrls;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings advertisements meta boxes class
 *
 * @access public
 */
class Component extends KocujIL\Classes\ComponentObject
{

    /**
     * Show author information (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $authorInfo = false;

    /**
     * Languages for author information
     *
     * @access private
     * @var array
     */
    private $authorInfoLanguages = array();

    /**
     * Set author information to display
     *
     * @access public
     * @param array $languages
     *            Set languages for which this information will be displayed; if empty, this information will be displayed for all languages - default: empty
     * @return void
     */
    public function setAuthorInfo(array $languages = array())
    {
        // set author information to display
        $this->authorInfo = true;
        $this->authorInfoLanguages = $languages;
    }

    /**
     * Action for adding information
     *
     * @access public
     * @return void
     */
    public function actionAdminInit()
    {
        // get locale
        $locale = get_locale();
        // show information about author links
        $this->getProjectObj()->getProjectKocujILObj()->get('settings-meta-boxes',
            ProjectCategory::BACKEND)->addSettingsMetaBox(Helper::getInstance()->getPrefix() . '_author_links',
            $this->getStrings('settings-adv-meta-boxes',
                ProjectCategory::BACKEND)->getString($this->getProjectObj()->getProjectKocujILObj()->getMainSettingType() === KocujIL\Enums\ProjectType::PLUGIN ? 'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TITLE_PLUGIN' : 'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TITLE_THEME'),
            $this->getStrings('settings-adv-meta-boxes',
                ProjectCategory::BACKEND)->getString($this->getProjectObj()->getProjectKocujILObj()->getMainSettingType() === KocujIL\Enums\ProjectType::PLUGIN ? 'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TEXT_1_PLUGIN' : 'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TEXT_1_THEME') . '<ul style="list-style:disc;margin-left:15px;">' . '<li>' . KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://' . Helper::getInstance()->getKocujPlUrl(),
                Helper::getInstance()->getKocujPlUrl(), array(
                    'external' => true
                )) . ': ' . $this->getStrings('settings-adv-meta-boxes',
                ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_WEBSITE_1') . '</li>' . '<li>' . KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://libs.kocuj.pl/',
                'libs.kocuj.pl', array(
                    'external' => true
                )) . ': ' . $this->getStrings('settings-adv-meta-boxes',
                ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_WEBSITE_2') . '</li>' . '<li>' . KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://github.com/kocuj/',
                'github.com/kocuj', array(
                    'external' => true
                )) . ': ' . $this->getStrings('settings-adv-meta-boxes',
                ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_WEBSITE_3') . '</li>' . '</ul>');
        // show author information
        if (($this->authorInfo) && ((empty ($this->authorInfoLanguages)) || (in_array($locale,
                    $this->authorInfoLanguages)))) {
            // show random author portfolio
            $portfolio = array(
                array(
                    'image' => 'gardeniaatelier-pl.jpg',
                    'name' => $this->getStrings('settings-adv-meta-boxes',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_WEBSITE_1'),
                    'url' => 'http://www.gardeniaatelier.pl/',
                    'demourl' => '',
                    'languages' => array(
                        'pl'
                    )
                ),
                array(
                    'image' => 'star-trek-horizon.jpg',
                    'name' => $this->getStrings('settings-adv-meta-boxes',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_WEBSITE_2'),
                    'url' => '',
                    'demourl' => 'http://star-trek-horizon.portfolio.kocuj.pl/',
                    'languages' => array(
                        'pl',
                        'en'
                    )
                ),
                array(
                    'image' => 'oaza-kapucyni-pl.jpg',
                    'name' => $this->getStrings('settings-adv-meta-boxes',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_WEBSITE_3'),
                    'url' => 'http://oaza.kapucyni.pl/',
                    'demourl' => '',
                    'languages' => array(
                        'pl'
                    )
                )
            );
            $pos = rand(0, count($portfolio) - 1);
            $this->getProjectObj()->getProjectKocujILObj()->get('settings-meta-boxes',
                ProjectCategory::BACKEND)->addSettingsMetaBox(Helper::getInstance()->getPrefix() . '_author_portfolio',
                ($this->getProjectObj()->getProjectKocujILObj()->getMainSettingType() === KocujIL\Enums\ProjectType::PLUGIN) ? $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_TITLE_PLUGIN') : $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_TITLE_THEME'),
                '<div style="margin:auto;text-align:center;">' . KocujIL\Classes\HtmlHelper::getInstance()->getImage(LibUrls::getInstance()->get('images') . '/project/components/backend/settings-adv-meta-boxes/' . $portfolio [$pos] ['image'],
                    array(
                        'style' => 'margin:auto;text-align:center;width:100%;max-width:250px;'
                    )) . '<br />' . '<strong>' . $portfolio [$pos] ['name'] . '</strong>' . '<br />' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_LANGUAGES') . ': ' . implode(', ',
                    $portfolio [$pos] ['languages']) . ($portfolio [$pos] ['url'] ? '<br />' . KocujIL\Classes\HtmlHelper::getInstance()->getLink($portfolio [$pos] ['url'],
                        $portfolio [$pos] ['url'], array(
                            'external' => true
                        )) : '') . ($portfolio [$pos] ['demourl'] ? '<br />' . KocujIL\Classes\HtmlHelper::getInstance()->getLink($portfolio [$pos] ['demourl'],
                        $this->getStrings('settings-adv-meta-boxes',
                            ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_SEE_DEMO'),
                        array(
                            'external' => true
                        )) : '') . '<br /><br />' . KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://kocuj.pl/portfolio/',
                    $this->getStrings('settings-adv-meta-boxes',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_MORE'),
                    array(
                        'external' => true
                    )) . '</div>');
            // show information about author
            $this->getProjectObj()->getProjectKocujILObj()->get('settings-meta-boxes',
                ProjectCategory::BACKEND)->addSettingsMetaBox(Helper::getInstance()->getPrefix() . '_author',
                $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TITLE'),
                sprintf($this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_1'),
                    KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://' . Helper::getInstance()->getKocujPlUrl(),
                        Helper::getInstance()->getKocujPlUrl(), array(
                            'external' => true
                        ))) . '<br /><br />' . '<strong>' . sprintf($this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_2'),
                    Helper::getInstance()->getKocujPlUrl()) . '</strong>' . '<ul style="list-style:disc;margin-left:15px;">' . '<li>' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_3') . '</li>' . '<li>' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_4') . '</li>' . '<li>' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_5') . '</li>' . '<li>' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_6') . '</li>' . '<li>' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_7') . '</li>' . '<li>' . $this->getStrings('settings-adv-meta-boxes',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_8') . '</li>' . '</ul>');
        }
    }
}
