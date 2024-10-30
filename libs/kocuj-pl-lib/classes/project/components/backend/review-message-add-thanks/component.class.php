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
namespace KocujPlLib\V13a\Classes\Project\Components\Backend\ReviewMessageAddThanks;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujPlLib\V13a\Enums\Project\Components\Backend\AddThanks\Display;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Adding thanks from review message class
 *
 * @access public
 */
class Component extends KocujIL\Classes\ComponentObject
{

    /**
     * Review message with adding thanks is set to be displayed (true) or review message with adding thanks will not be displayed (false)
     *
     * @access private
     * @var bool
     */
    private $reviewMessageAddThanksDisplay = false;

    /**
     * Action for displaying CSS stylesheet
     *
     * @access public
     * @return void
     */
    public function adminHead()
    {
        // check if thanks has not been added already
        if ($this->getComponent('add-thanks', KocujIL\Enums\ProjectCategory::BACKEND)->getThanksAddedOptionValue()) {
            return;
        }
        // add styles
        echo '<style id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_review_message_style') . '" type="text/css" media="all">' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_review_message_more_info_link_div' . PHP_EOL . '{' . PHP_EOL . 'font-size: 11px;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getProjectObj()->getProjectKocujILObj()->get('project-helper')->getPrefix() . '__review_message_element_div {' . PHP_EOL . 'height: 50px;' . PHP_EOL . '}' . PHP_EOL . '@media screen and (max-width: 782px) {' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_review_message_more_info_link_div' . PHP_EOL . '{' . PHP_EOL . 'font-size: 12px;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getProjectObj()->getProjectKocujILObj()->get('project-helper')->getPrefix() . '__review_message_element_div {' . PHP_EOL . 'height: auto;' . PHP_EOL . '}' . PHP_EOL . '}' . PHP_EOL . '</style>' . PHP_EOL;
    }

    /**
     * Filter for element class in review message
     *
     * @access public
     * @param string $class
     *            Class
     * @return string Class
     */
    public function filterReviewMessageElementClass($class)
    {
        // exit
        return $class . ((isset ($class [0]) /* strlen($class) > 0 */) ? ' ' : '') . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_review_message_element_div';
    }

    /**
     * Filter for elements before other elements in review message
     *
     * @access public
     * @param array $elements
     *            Elements
     * @return array Elements
     */
    public function filterReviewMessageElementsBefore(array $elements)
    {
        // check if thanks has not been added already
        if ($this->getComponent('add-thanks', KocujIL\Enums\ProjectCategory::BACKEND)->getThanksAddedOptionValue()) {
            return $elements;
        }
        // add element
        $elements [] = '<input type="button" class="button button-small" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_review_message_send') . '" value="' . esc_attr($this->getStrings('review-message-add-thanks',
                KocujIL\Enums\ProjectCategory::BACKEND)->getString('FILTER_REVIEW_MESSAGE_ELEMENTS_BEFORE_ADD_THANKS')) . '" />' . '<br />' . '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__add_thanks_review_message_more_info_link_div') . '">&nbsp;</div>';
        // add scripts
        $this->getComponent('add-thanks',
            KocujIL\Enums\ProjectCategory::BACKEND)->setAddThanksDisplay(Display::YES);
        // set review message with adding thanks to be displayed
        $this->reviewMessageAddThanksDisplay = true;
        // exit
        return $elements;
    }

    /**
     * Action for scripts
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // initialize review message script
        if ($this->reviewMessageAddThanksDisplay) {
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
                            kocujPLV13aBackendAddThanks.setReviewMessageAddThanks('<?php echo esc_js($this->getProjectObj()->getProjectKocujILObj()->getMainSettingInternalName()); ?>');
                        });
                    }(jQuery));
                    /* ]]> */
                </script>
                <?php
            }
        }
    }
}
