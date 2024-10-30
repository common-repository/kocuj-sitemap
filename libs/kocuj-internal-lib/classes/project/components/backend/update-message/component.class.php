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
namespace KocujIL\V13a\Classes\Project\Components\Backend\UpdateMessage;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\HtmlHelper;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Enums\Project\Components\All\Window\Type;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable;
use KocujIL\V13a\Enums\Project\Components\Backend\UpdateMessage\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Update message class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Update messages
     *
     * @access private
     * @var array
     */
    private $updateMessage = array();

    /**
     * Dividing string for multiple messages for one version update
     *
     * @access private
     * @var string
     */
    private $divideString = '<br /><br /><hr /><br />';

    /**
     * Message will be displayed (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $messageDisplay = array();

    /**
     * Top message will be displayed (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $topMessageDisplay = array();

    /**
     * Get option name for version for last update message
     *
     * @access public
     * @return string Option name for version for last update message
     */
    public static function getOptionNameLastUpdateMessageVersion()
    {
        // exit
        return 'last_update_msg_version';
    }

    /**
     * Get version for last update message from database
     *
     * @access public
     * @return string Version for last update message from database
     */
    public function getLastUpdateMessageVersionOptionValue()
    {
        // exit
        return $this->getComponent('meta')->get(self::getOptionNameLastUpdateMessageVersion());
    }

    /**
     * Set dividing string for multiple message for one version update
     *
     * @access public
     * @param string $divideString
     *            Dividing string
     * @return void
     */
    public function setDivideString($divideString)
    {
        // set dividing string
        $this->divideString = $divideString;
    }

    /**
     * Add message for update from the selected version
     *
     * @access public
     * @param string $fromVersion
     *            Version from which update will show message; it can be only fragment of version number, but then it must be ended with dot character ("."), for example, "1.", "1.0.", etc.
     * @param string $message
     *            Message to display
     * @param int $useTopMessage
     *            Use top message with link to message to display or just show message; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage: NO (when display just show message) or YES (when display top message with link to message to display) - default: \KocujIL\V13a\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::NO
     * @return void
     */
    public function addUpdateMessage(
        $fromVersion,
        $message,
        $useTopMessage = UseTopMessage::NO
    ) {
        // set update message
        if (isset ($this->updateMessage [$fromVersion])) {
            $this->updateMessage [$fromVersion] ['message'] .= $this->divideString . $message;
        } else {
            $this->updateMessage [$fromVersion] = array(
                'message' => $message,
                'usetopmessage' => $useTopMessage
            );
            uasort($this->updateMessage, function ($a, $b) {
                return -strcasecmp($a ['message'], $b ['message']);
            });
        }
    }

    /**
     * Get messages for update data
     *
     * @access public
     * @return array Messages for update data; each message for update data has the following fields: "message" (string type; update message), "usetopmessage" (int type; if set to \KocujIL\V13a\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES, top message will be displayed with link to update message)
     */
    public function getMessagesForUpdate()
    {
        // exit
        return $this->updateMessage;
    }

    /**
     * Get message or top message for update from the selected version
     *
     * @access private
     * @param bool $isMessage
     *            Message will be returned (true) or top message (false)
     * @param string $fromVersion
     *            Version from which update will show message
     * @return bool|int|string Message text or check top message for the selected version or false if not exists
     */
    private function getMessageOrTopMessage($isMessage, $fromVersion)
    {
        // initialize
        $output = '';
        // get message for version
        if (!empty ($this->updateMessage)) {
            $index = ($isMessage) ? 'message' : 'usetopmessage';
            foreach ($this->updateMessage as $key => $val) {
                $addText = '';
                $keyLength = strlen($key);
                if ($key [$keyLength - 1] !== '.') {
                    if ($key === $fromVersion) {
                        if ($isMessage) {
                            $addText = $val [$index];
                        } else {
                            return $val [$index];
                        }
                    }
                } else {
                    if ($key === substr($fromVersion, 0, $keyLength)) {
                        if ($isMessage) {
                            $addText = $val [$index];
                        } else {
                            return $val [$index];
                        }
                    }
                }
                if (isset ($addText [0]) /* strlen($addText) > 0 */) {
                    if (isset ($output [0]) /* strlen($output) > 0 */) {
                        $output .= $this->divideString;
                    }
                    $output .= $addText;
                }
            }
        }
        // exit
        return (isset ($output [0]) /* strlen($output) > 0 */) ? $output : false;
    }

    /**
     * Check if message for update from the selected version exists
     *
     * @access public
     * @param string $fromVersion
     *            Version from which update will show message
     * @return bool Message for update from the selected version exists (true) or not (false)
     */
    public function checkMessage($fromVersion)
    {
        // get message
        $message = $this->getMessageOrTopMessage(true, $fromVersion);
        // exit
        return (isset ($message [0]) /* strlen($message) > 0 */);
    }

    /**
     * Get message for update from the selected version
     *
     * @access public
     * @param string $fromVersion
     *            Version from which update will show message
     * @return bool|string Message for the selected version or false if not exists
     */
    public function getMessage($fromVersion)
    {
        // exit
        return $this->getMessageOrTopMessage(true, $fromVersion);
    }

    /**
     * Check top message for update from the selected version
     *
     * @access public
     * @param string $fromVersion
     *            Version from which update will show message
     * @return bool Top message for the selected version exists (true) or not (false)
     */
    public function checkTopMessage($fromVersion)
    {
        // exit
        return ($this->getMessageOrTopMessage(false, $fromVersion) === UseTopMessage::YES);
    }

    /**
     * Remove message for update
     *
     * @access public
     * @param string $fromVersion
     *            Version from which update will show message
     * @return void
     * @throws Exception
     */
    public function removeMessageForUpdate($fromVersion)
    {
        // check if this message for update identifier exists
        if (!isset ($this->updateMessage [$fromVersion])) {
            throw new Exception ($this, ExceptionCode::UPDATE_MESSAGE_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $fromVersion);
        }
        // remove message for update
        unset ($this->updateMessage [$fromVersion]);
    }

    /**
     * Action for preparing update message
     *
     * @access public
     * @return void
     */
    public function actionAdminInit()
    {
        // optionally show message or message about this update
        if ((current_user_can('manage_network')) || (current_user_can('manage_options'))) {
            $oldVersion = $this->getComponent('version', ProjectCategory::ALL)->getOldVersionOptionValue();
            if ($oldVersion !== false) {
                $lastMsgVersion = $this->getLastUpdateMessageVersionOptionValue();
                if (($lastMsgVersion !== $this->getComponent('version',
                            ProjectCategory::ALL)->getCurrentVersion()) && ($this->checkMessage($oldVersion))) {
                    // set security nonce
                    $nonce = wp_create_nonce(Helper::getInstance()->getPrefix() . '__version_info');
                    // load scripts
                    $this->getComponent('js-ajax', ProjectCategory::ALL)->addAjaxJs();
                    JsHelper::getInstance()->addLibScript('backend-update-message',
                        'project/components/backend/update-message', 'update-message', array(
                            'helper'
                        ), array(
                            Helper::getInstance()->getPrefix() . '-all-js-ajax',
                            Helper::getInstance()->getPrefix() . '-all-window'
                        ), 'kocujILV13aBackendUpdateMessageVals', array(
                            'prefix' => Helper::getInstance()->getPrefix(),
                            'security' => $nonce
                        ));
                    // add window
                    $this->getComponent('window', ProjectCategory::ALL)->addWindow('update_message',
                        sprintf($this->getStrings('update-message',
                            ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_WINDOW_TITLE'),
                            $this->getComponent('version', ProjectCategory::ALL)->getCurrentVersion()), 400, 400,
                        Type::AJAX, array(
                            'url' => admin_url('admin-ajax.php'),
                            'ajaxdata' => array(
                                'action' => $this->getComponent('project-helper')->getPrefix() . '__update_message_display',
                                'security' => $nonce,
                                'projectVersionFrom' => $oldVersion,
                                'projectVersionTo' => $this->getComponent('version',
                                    ProjectCategory::ALL)->getCurrentVersion()
                            )
                        ));
                    // check if display top message
                    $isTopMessage = $this->checkTopMessage($oldVersion);
                    // set message to display
                    $this->messageDisplay = true;
                    $this->topMessageDisplay = $isTopMessage;
                    // optionally show top message
                    if ($isTopMessage) {
                        if ($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) {
                            $message = $this->getStrings('update-message',
                                ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_TOP_MESSAGE_PLUGIN');
                        } else {
                            $message = $this->getStrings('update-message',
                                ProjectCategory::BACKEND)->getString('ACTION_ADMIN_INIT_TOP_MESSAGE_THEME');
                        }
                        $this->getComponent('message',
                            ProjectCategory::BACKEND)->addMessageForAllPages('update_message',
                            sprintf($message, $this->getProjectObj()->getMainSettingTitleOriginal(),
                                HtmlHelper::getInstance()->getLinkBegin('#', array(
                                    'id' => $this->getComponent('project-helper')->getPrefix() . '__update_message_link'
                                )), HtmlHelper::getInstance()->getLinkEnd()),
                            \KocujIL\V13a\Enums\Project\Components\Backend\Message\Type::INFORMATION,
                            Closable::CLOSABLE_TEMPORARY);
                    }
                } else {
                    // add information that message has been displayed
                    $this->getComponent('meta')->addOrUpdate(self::getOptionNameLastUpdateMessageVersion(),
                        $this->getComponent('version', ProjectCategory::ALL)->getCurrentVersion());
                }
            }
        }
    }

    /**
     * Action for displaying update message
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // initialize message script
        if (!empty ($this->messageDisplay)) {
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function ($) {
                    $(document).ready(function () {
                        kocujILV13aBackendUpdateMessage.addProject('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>');
                        <?php if ($this->topMessageDisplay) : ?>
                        $('#<?php echo esc_js($this->getComponent('project-helper')->getPrefix() . '__update_message_link'); ?>').click(function () {
                            <?php echo $this->getComponent('window',
                            ProjectCategory::ALL)->getWindowJsCode('update_message'); ?>
                        });
                        $('#<?php echo esc_js($this->getComponent('project-helper')->getPrefix() . '__message_update_message'); ?> .notice-dismiss').click(function () {
                            kocujILV13aBackendUpdateMessage.sendCloseEvent('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>');
                        });
                        <?php else : ?>
                        <?php echo $this->getComponent('window',
                        ProjectCategory::ALL)->getWindowJsCode('update_message'); ?>
                        <?php endif; ?>
                    });
                }(jQuery));
                /* ]]> */
            </script>
            <?php
        }
    }
}
