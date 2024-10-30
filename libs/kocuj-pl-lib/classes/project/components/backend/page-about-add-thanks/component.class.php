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
namespace KocujPlLib\V13a\Classes\Project\Components\Backend\PageAboutAddThanks;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujPlLib\V13a\Classes\LibUrls;
use KocujPlLib\V13a\Enums\Project\Components\Backend\AddThanks\Display;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Adding thanks from page about class
 *
 * @access public
 */
class Component extends KocujIL\Classes\ComponentObject
{

    /**
     * Page about with adding thanks is set to be displayed (true) or page about with adding thanks will not be displayed (false)
     *
     * @access private
     * @var bool
     */
    private $pageAboutAddThanksDisplay = false;

    /**
     * Action for displaying adding thanks button in page about
     *
     * @access public
     * @return void
     */
    public function actionAfterPageAbout()
    {
        // check if thanks has not been added already
        if ($this->getComponent('add-thanks', KocujIL\Enums\ProjectCategory::BACKEND)->getThanksAddedOptionValue()) {
            return;
        }
        // add styles
        echo '<style scoped="scoped" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__page_about_add_thanks_style') . '" type="text/css" media="all">' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_send' . PHP_EOL . '{' . PHP_EOL . 'margin-bottom: 0;' . PHP_EOL . '}' . PHP_EOL . '@media screen and (max-width: 782px) {' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_send' . PHP_EOL . '{' . PHP_EOL . 'white-space: normal;' . PHP_EOL . 'width: 100%;' . PHP_EOL . '}' . PHP_EOL . '}' . PHP_EOL . '</style>' . PHP_EOL;
        // show adding thanks
        echo '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_div') . '">';
        echo '<hr />';
        echo '<br />';
        if ($this->getProjectObj()->getProjectKocujILObj()->getMainSettingType() === KocujIL\Enums\ProjectType::PLUGIN) {
            echo $this->getStrings('page-about-add-thanks',
                KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_AFTER_PAGE_ABOUT_TEXT_PLUGIN');
        } else {
            echo $this->getStrings('page-about-add-thanks',
                KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_AFTER_PAGE_ABOUT_TEXT_THEME');
        }
        echo '<br />';
        printf($this->getStrings('page-about-add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_AFTER_PAGE_ABOUT_TEXT'),
            KocujIL\Classes\HtmlHelper::getInstance()->getLinkBegin('#', array(
                'id' => $this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_more_info_link'
            )), KocujIL\Classes\HtmlHelper::getInstance()->getLinkEnd());
        echo '<br /><br />';
        echo '<input type="button" class="button button-small" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_send') . '" value="' . $this->getStrings('page-about-add-thanks',
                KocujIL\Enums\ProjectCategory::BACKEND)->getString('ACTION_AFTER_PAGE_ABOUT_ADD_THANKS') . '" />';
        echo '<br />';
        echo '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_status_div') . '" style="display:none;">&nbsp;</div>';
        echo '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_page_about_loading_div') . '" style="display:none;">';
        echo KocujIL\Classes\HtmlHelper::getInstance()->getImage(LibUrls::getInstance()->get('images') . '/project/components/backend/page-about-add-thanks/loading.gif');
        echo '</div>';
        echo '</div>';
        // add scripts
        $this->getComponent('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->setAddThanksDisplay(Display::YES);
        // set page about with adding thanks to be displayed
        $this->pageAboutAddThanksDisplay = true;
    }

    /**
     * Action for scripts
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // initialize page about script
        if ($this->pageAboutAddThanksDisplay) {
            // check if window exists
            $exists = $this->getProjectObj()->getProjectKocujILObj()->get('window',
                KocujIL\Enums\ProjectCategory::ALL)->checkWindow('add_thanks');
            if ($exists) {
                // get window function
                $jsFunctionName = '';
                $jsFunction = $this->getProjectObj()->getProjectKocujILObj()->get('window',
                    KocujIL\Enums\ProjectCategory::ALL)->getWindowJsFunction('add_thanks', $jsFunctionName);
                // show script
                ?>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    <?php echo $jsFunction; ?>
                    (function ($) {
                        $(document).ready(function () {
                            kocujPLV13aBackendAddThanks.addProjectIfNotExists('<?php echo esc_js($this->getProjectObj()->getProjectKocujILObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($jsFunctionName); ?>', '<?php echo esc_js($this->getProjectObj()->getSettingArray('api',
                                'url')); ?>', '<?php echo esc_js($this->getProjectObj()->getSettingArray('api',
                                'login')); ?>', '<?php echo esc_js($this->getProjectObj()->getSettingArray('api',
                                'password')); ?>');
                            kocujPLV13aBackendAddThanks.setPageAboutAddThanks('<?php echo esc_js($this->getProjectObj()->getProjectKocujILObj()->getMainSettingInternalName()); ?>');
                        });
                    }(jQuery));
                    /* ]]> */
                </script>
                <?php
            }
        }
    }
}
