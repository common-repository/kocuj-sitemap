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
namespace KocujIL\V13a\Classes\Project\Components\Backend\ReviewMessage;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\HtmlHelper;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Classes\LibUrls;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\Type;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Review message class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Days count after which message about review will be displayed; 0 means never
     *
     * @access private
     * @var int
     */
    private $days = 0;

    /**
     * Vote URL
     *
     * @access private
     * @var string
     */
    private $voteUrl = '';

    /**
     * Review message is set to be displayed (true) or review message will not be displayed (false)
     *
     * @access private
     * @var bool
     */
    private $reviewMessageDisplay = false;

    /**
     * Set days count after which message about review will be displayed; 0 means never
     *
     * @access public
     * @param int $days
     *            Days count after which message about review will be displayed; 0 means never
     * @return void
     */
    public function setDays($days)
    {
        // set days count
        $this->days = $days;
    }

    /**
     * Get days count after which message about review will be displayed; 0 means never
     *
     * @access public
     * @return int Days count after which message about review will be displayed; 0 means never
     */
    public function getDays()
    {
        // exit
        return $this->days;
    }

    /**
     * Set vote URL
     *
     * @access public
     * @param string $url
     *            Vote url
     * @return void
     */
    public function setVoteUrl($url)
    {
        // set value
        $this->voteUrl = $url;
    }

    /**
     * Set vote URL
     *
     * @access public
     * @return string Vote url
     */
    public function getVoteUrl()
    {
        // exit
        return $this->voteUrl;
    }

    /**
     * Get window has been closed (true) or not (false)
     *
     * @access public
     * @return bool Window has been closed (true) or not (false)
     */
    public function getClosedWindowOptionValue()
    {
        // exit
        return $this->getComponent('message', ProjectCategory::BACKEND)->checkMessageClosed('review');
    }

    /**
     * Get message element in "div" container
     *
     * @access private
     * @param string $element
     *            Message element
     * @param bool $smaller
     *            Message element is smaller than normal (true) or not (false) - default: false
     * @return string Message element in "div" container
     */
    private function getMessageElement($element, $smaller = false)
    {
        // set horizontal margins
        $margin = ($smaller) ? 2 : 5;
        // exit
        return '<div ' . $this->getComponent('project-helper')->applyFiltersForHTMLStyleAndClass('review_message_element' . (($smaller) ? '_small' : ''),
                '', array(
                    'defaultstyle' => 'margin-left:' . $margin . 'px;margin-right:' . $margin . 'px;vertical-align:top;',
                    'defaultclass' => $this->getComponent('project-helper')->getPrefix() . '__review_message_element_' . (($smaller) ? 'small_' : '') . 'div'
                )) . '>' . $element . '</div>';
    }

    /**
     * Add additional elements
     *
     * @access private
     * @param array $elements
     *            Additional elements to add
     * @return string Additional elements in string
     */
    private function addAdditionalElements(array $elements)
    {
        // check if there are any additional elements
        if (empty ($elements)) {
            return '';
        }
        // add additional elements
        $output = '';
        foreach ($elements as $element) {
            $output .= $this->getMessageElement($element);
        }
        // exit
        return $output;
    }

    /**
     * Action for display review message
     *
     * @access public
     * @return void
     */
    public function actionAdminHead()
    {
        // check if display review message
        if ((!isset ($this->voteUrl [0]) /* strlen($this->voteUrl) === 0 */) || ($this->getClosedWindowOptionValue()) || ($this->days === 0)) {
            return;
        }
        // get installation date
        $date = $this->getComponent('installation-date', ProjectCategory::BACKEND)->getInstallationDateOptionValue();
        if ($date === false) {
            return;
        }
        // check days count
        $days = ( int )((strtotime(date('Y-m-d') . ' 23:59:59') - strtotime($date . ' 00:00:00')) / 86400);
        // optionally show review message
        if ($days > $this->days) {
            // add CSS stylesheet
            echo '<style id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_style') . '" type="text/css" media="all">' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__review_message_news_channels_header_div' . PHP_EOL . '{' . PHP_EOL . 'font-size: 11px;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__review_message_element_div' . PHP_EOL . '{' . PHP_EOL . 'float: left;' . PHP_EOL . 'margin-bottom: 10px;' . PHP_EOL . 'height: 30px;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__review_message_element_div .button' . PHP_EOL . '{' . PHP_EOL . 'margin-bottom: 0;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__review_message_element_small_div' . PHP_EOL . '{' . PHP_EOL . 'float: left;' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__review_message_news_container_inside' . PHP_EOL . '{' . PHP_EOL . 'float: left;' . PHP_EOL . '}' . PHP_EOL . '@media screen and (max-width: 782px) {' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__review_message_buttons_container' . PHP_EOL . '{' . PHP_EOL . 'overflow: visible;' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__review_message_news_channels_header_div' . PHP_EOL . '{' . PHP_EOL . 'font-size: 12px;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__review_message_element_div' . PHP_EOL . '{' . PHP_EOL . 'margin-top: 10px;' . PHP_EOL . 'margin-bottom: 10px;' . PHP_EOL . 'width: 100%;' . PHP_EOL . 'height: auto;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__review_message_element_div .button' . PHP_EOL . '{' . PHP_EOL . 'white-space: normal;' . PHP_EOL . 'width: 100%;' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__review_message_news_container_inside' . PHP_EOL . '{' . PHP_EOL . 'clear: both;' . PHP_EOL . '}' . PHP_EOL . '}' . PHP_EOL . '</style>' . PHP_EOL;
            // show review message beginning
            $message = $this->getComponent('project-helper')->applyFilters('before_review_message');
            if ($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) {
                $message .= sprintf($this->getStrings('review-message',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_PLUGIN_DAYS'),
                    $this->getProjectObj()->getMainSettingTitleOriginal(), $this->days);
                $message .= ' ' . $this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_THINGS_SUPPORT_PLUGIN') . ' ';
            } else {
                $message .= sprintf($this->getStrings('review-message',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_THEME_DAYS'),
                    $this->getProjectObj()->getMainSettingTitleOriginal(), $this->days);
                $message .= ' ' . $this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_THINGS_SUPPORT_THEME') . ' ';
            }
            $message .= '<br />';
            // show buttons for telling others
            $message .= '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_buttons_container') . '">';
            $elements = $this->getComponent('project-helper')->applyFilters('review_message_elements_before', '', '',
                array());
            $message .= $this->addAdditionalElements($elements);
            $message .= $this->getMessageElement('<input type="button" class="button button-small" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_vote') . '" value="' . esc_attr(($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) ? $this->getStrings('review-message',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_VOTE_PLUGIN') : $this->getStrings('review-message',
                    ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_VOTE_THEME')) . '" />');
            if ($this->getProjectObj()->getSettingArray('tellothers', 'facebook')) {
                $message .= $this->getMessageElement('<input type="button" class="button button-small" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_facebook') . '" value="' . esc_attr($this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_FACEBOOK')) . '" />');
            }
            if ($this->getProjectObj()->getSettingArray('tellothers', 'twitter')) {
                $message .= $this->getMessageElement('<input type="button" class="button button-small" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_twitter') . '" value="' . esc_attr($this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_TWITTER')) . '" />');
            }
            $translation = $this->getProjectObj()->getSettingArray('repository', 'translation');
            if (isset ($translation [0]) /* strlen($translation) > 0 */) {
                $message .= $this->getMessageElement('<input type="button" class="button button-small" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_translation') . '" value="' . esc_attr($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN ? $this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_TRANSLATION_PLUGIN') : $this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_TRANSLATION_THEME')) . '" />');
            }
            $elements = $this->getComponent('project-helper')->applyFilters('review_message_elements_after', '', '',
                array());
            $message .= $this->addAdditionalElements($elements);
            $message .= '</div>';
            $message .= '<div style="clear:both;"></div>';
            // show news channels links
            $linkRSS = $this->getProjectObj()->getSettingArray('newschannels', 'rss');
            $linkFacebook = $this->getProjectObj()->getSettingArray('newschannels', 'facebook');
            $linkTwitter = $this->getProjectObj()->getSettingArray('newschannels', 'twitter');
            if ((isset ($linkRSS [0]) /* strlen($linkRSS) > 0 */) || (isset ($linkFacebook [0]) /* strlen($linkFacebook) > 0 */) || (isset ($linkTwitter [0]) /* strlen($linkTwitter) > 0 */)) {
                $message .= '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_news_container') . '">';
                if ($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) {
                    $text = $this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_NEWS_CHANNELS_TEXT_PLUGIN');
                } else {
                    $text = $this->getStrings('review-message',
                        ProjectCategory::BACKEND)->getString('ACTION_ADMIN_HEAD_NEWS_CHANNELS_TEXT_THEME');
                }
                $message .= $this->getMessageElement('<div id="' . $this->getComponent('project-helper')->getPrefix() . '__review_message_news_channels_header_div"><em>' . $text . ':</em></div>',
                    true);
                $message .= '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__review_message_news_container_inside') . '">';
                if (isset ($linkRSS [0]) /* strlen($linkRSS) > 0 */) {
                    $message .= $this->getMessageElement(HtmlHelper::getInstance()->getLink($linkRSS,
                        HtmlHelper::getInstance()->getImage(LibUrls::getInstance()->get('images') . '/project/components/backend/review-message/rss.png'),
                        array(
                            'external' => true
                        )), true);
                }
                if (isset ($linkFacebook [0]) /* strlen($linkFacebook) > 0 */) {
                    $message .= $this->getMessageElement(HtmlHelper::getInstance()->getLink($linkFacebook,
                        HtmlHelper::getInstance()->getImage(LibUrls::getInstance()->get('images') . '/project/components/backend/review-message/facebook.png'),
                        array(
                            'external' => true
                        )), true);
                }
                if (isset ($linkTwitter [0]) /* strlen($linkTwitter) > 0 */) {
                    $message .= $this->getMessageElement(HtmlHelper::getInstance()->getLink($linkTwitter,
                        HtmlHelper::getInstance()->getImage(LibUrls::getInstance()->get('images') . '/project/components/backend/review-message/twitter.png'),
                        array(
                            'external' => true
                        )), true);
                }
                $message .= '</div>';
                $message .= '</div>';
                $message .= '<div style="clear:both;"></div>';
            }
            // show review message end
            $message .= $this->getComponent('project-helper')->applyFilters('after_review_message');
            $this->getComponent('message', ProjectCategory::BACKEND)->addMessageForAllPages('review', $message,
                Type::INFORMATION, Closable::CLOSABLE);
            // add scripts
            JsHelper::getInstance()->addLibScript('backend-review-message', 'project/components/backend/review-message',
                'review-message', array(
                    'helper'
                ), array(), 'kocujILV13aBackendReviewMessageVals', array(
                    'prefix' => Helper::getInstance()->getPrefix(),
                    'security' => wp_create_nonce(Helper::getInstance()->getPrefix() . '__review_message')
                ));
            // set review message to be displayed
            $this->reviewMessageDisplay = true;
        }
    }

    /**
     * Action for forcing displaying of review message
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // initialize review message script
        if ($this->reviewMessageDisplay) {
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function ($) {
                    $(document).ready(function () {
                        kocujILV13aBackendReviewMessage.addProject('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($this->voteUrl); ?>', '<?php echo esc_js($this->getProjectObj()->getSettingArray('tellothers',
                            'facebook')); ?>', '<?php echo esc_js($this->getProjectObj()->getSettingArray('tellothers',
                            'twitter')); ?>', '<?php echo esc_js($this->getProjectObj()->getSettingArray('repository',
                            'translation')); ?>');
                        kocujILV13aBackendReviewMessage.setEvents('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>');
                    });
                }(jQuery));
                /* ]]> */
            </script>
            <?php
        }
    }
}
