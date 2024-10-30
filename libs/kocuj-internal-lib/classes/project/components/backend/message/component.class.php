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
namespace KocujIL\V13a\Classes\Project\Components\Backend\Message;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\DbDataHelper;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Classes\Version;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\Type;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Message class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Messages
     *
     * @access private
     * @var array
     */
    private $messages = array();

    /**
     * Get option name for closed message
     *
     * @access public
     * @return string Option name for closed message
     */
    public static function getOptionNameMessageClosed()
    {
        // exit
        return 'msg_closed';
    }

    /**
     * Add message
     *
     * @access private
     * @param string $id
     *            Message identifier; must be unique
     * @param string $content
     *            Message content
     * @param int $type
     *            Message type; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\Message\Type: INFORMATION (for information message), WARNING (for warning message), ERROR (for error message) or SUCCESS (for success message)
     * @param int $closable
     *            Message closable status; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable: NOT_CLOSABLE (for message without closing button), CLOSABLE (for message with closing button and with saving information about closed message) or CLOSABLE_TEMPORARY (for message with closing button, but without saving information about closed message)
     * @param bool $allPages
     *            Message will be displayed on all pages in administration panel (true) or not (false)
     * @param array $pages
     *            Settings pages list for current project where this message should be displayed; it should be identifiers from settings page for current project only; if it is empty, this message will be displayed on all settings pages for current project
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; CSS class for message div), "closecallback" (array or string type; if message is closable, it is callback executed during closing the message), "permissions" (array type; permissions for message to display; if empty, no permissions are required to display this message), "style" (string type; CSS style for message div)
     * @return void
     * @throws Exception
     */
    private function addMessageForProjectOrAllPages(
        $id,
        $content,
        $type,
        $closable,
        $allPages,
        array $pages,
        array $attr
    ) {
        // check if this identifier does not already exists
        if (isset ($this->messages [$id])) {
            throw new Exception ($this, ExceptionCode::MESSAGE_ID_EXISTS, __FILE__, __LINE__, $id);
        }
        // add message
        $this->messages [$id] = array(
            'content' => $content
        );
        if ($type !== Type::INFORMATION) {
            $this->messages [$id] ['type'] = $type;
        }
        if ($closable !== Closable::NOT_CLOSABLE) {
            $this->messages [$id] ['closable'] = $closable;
        }
        if ($allPages) {
            $this->messages [$id] ['allpages'] = true;
        } else {
            $this->messages [$id] ['pages'] = $pages;
        }
        if ((isset ($attr ['class'])) && (isset ($attr ['class'] [0]) /* strlen($attr['class']) > 0 */)) {
            $this->messages [$id] ['class'] = $attr ['class'];
        }
        if ((isset ($attr ['style'])) && (isset ($attr ['style'] [0]) /* strlen($attr['style']) > 0 */)) {
            $this->messages [$id] ['style'] = $attr ['style'];
        }
        if (isset ($attr ['permissions'])) {
            $this->messages [$id] ['permissions'] = $attr ['permissions'];
        }
        if ((($closable === Closable::CLOSABLE) || ($closable === Closable::CLOSABLE_TEMPORARY)) && (isset ($attr ['closecallback']))) {
            $this->messages [$id] ['closecallback'] = $attr ['closecallback'];
        }
    }

    /**
     * Add message
     *
     * @access public
     * @param string $id
     *            Message identifier; must be unique
     * @param string $content
     *            Message content
     * @param int $type
     *            Message type; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\Message\Type: INFORMATION (for information message), WARNING (for warning message), ERROR (for error message) or SUCCESS (for success message) - default: \KocujIL\V13a\Enums\Project\Components\Backend\Message\Type::INFORMATION
     * @param int $closable
     *            Message closable status; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable: NOT_CLOSABLE (for message without closing button) or CLOSABLE (for message with closing button) - default: \KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable::NOT_CLOSABLE
     * @param array $pages
     *            Settings pages list for current project where this message should be displayed; it should be identifiers from settings page for current project only; if it is empty, this message will be displayed on all settings pages for current project - default: empty
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; CSS class for message div), "closecallback" (array or string type; if message is closable, it is callback executed during closing the message), "permissions" (array type; permissions for message to display; if empty, no permissions are required to display this message), "style" (string type; CSS style for message div) - default: empty
     * @return void
     * @throws Exception
     */
    public function addMessage(
        $id,
        $content,
        $type = Type::INFORMATION,
        $closable = Closable::NOT_CLOSABLE,
        array $pages = array(),
        array $attr = array()
    ) {
        // add message
        $this->addMessageForProjectOrAllPages($id, $content, $type, $closable, false, $pages, $attr);
    }

    /**
     * Add message for all pages in administration panel
     *
     * @access public
     * @param string $id
     *            Message identifier; must be unique
     * @param string $content
     *            Message content
     * @param int $type
     *            Message type; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\Message\Type: INFORMATION (for information message), WARNING (for warning message), ERROR (for error message) or SUCCESS (for success message) - default: \KocujIL\V13a\Enums\Project\Components\Backend\Message\Type::INFORMATION
     * @param int $closable
     *            Message closable status; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable: NOT_CLOSABLE (for message without closing button) or CLOSABLE (for message with closing button) - default: \KocujIL\V13a\Enums\Project\Components\Backend\Message\Closable::NOT_CLOSABLE
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; CSS class for message div), "closecallback" (array or string type; if message is closable, it is callback executed during closing the message), "permissions" (array type; permissions for message to display; if empty, no permissions are required to display this message), "style" (string type; CSS style for message div) - default: empty
     * @return void
     * @throws Exception
     */
    public function addMessageForAllPages(
        $id,
        $content,
        $type = Type::INFORMATION,
        $closable = Closable::NOT_CLOSABLE,
        array $attr = array()
    ) {
        // add message
        $this->addMessageForProjectOrAllPages($id, $content, $type, $closable, true, array(), $attr);
    }

    /**
     * Get messages data
     *
     * @access public
     * @return array Messages data; each message data has the following fields: "allpages" (if it is set to true, message will be displayed in all pages in administration panel), "closecallback" (array or string type; if message is closable, it is callback executed during closing the message), "class" (string type; CSS class for message div), "content" (content of message), "pages" (settings pages list for current project on which message is displayed or empty if it is displayed on all settings pages for current project), "permissions" (array type; permissions for message to display; if empty, no permissions are required to display this message), "style" (string type; CSS style for message div), "type" (type of message)
     */
    public function getMessages()
    {
        // prepare messages
        $messages = $this->messages;
        foreach ($messages as $key => $val) {
            if (!isset ($val ['type'])) {
                $messages [$key] ['type'] = Type::INFORMATION;
            }
            if (!isset ($val ['closable'])) {
                $messages [$key] ['closable'] = Closable::NOT_CLOSABLE;
            }
            if (!isset ($val ['allpages'])) {
                $messages [$key] ['allpages'] = false;
            }
            if (!isset ($val ['pages'])) {
                $messages [$key] ['pages'] = array();
            }
            if (!isset ($val ['class'])) {
                $messages [$key] ['class'] = '';
            }
            if (!isset ($val ['style'])) {
                $messages [$key] ['style'] = '';
            }
            if (!isset ($val ['permissions'])) {
                $messages [$key] ['permissions'] = array();
            }
            if (!isset ($val ['closecallback'])) {
                $messages [$key] ['closecallback'] = '';
            }
        }
        // exit
        return $messages;
    }

    /**
     * Check if message exists
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return bool Message exists (true) or not (false)
     */
    public function checkMessage($id)
    {
        // exit
        return isset ($this->messages [$id]);
    }

    /**
     * Get message data by id
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return array|bool Message data or false if not exists; message data have the following fields: "allpages" (if it is set to true, message will be displayed in all pages in administration panel), "closecallback" (array or string type; if message is closable, it is callback executed during closing the message), "class" (string type; CSS class for message div), "content" (content of message), "pages" (settings pages list for current project on which message is displayed or empty if it is displayed on all settings pages for current project), "permissions" (array type; permissions for message to display; if empty, no permissions are required to display this message), "style" (string type; CSS style for message div), "type" (type of message)
     */
    public function getMessage($id)
    {
        // get messages
        $messages = $this->getMessages();
        // exit
        return (isset ($messages [$id])) ? $messages [$id] : false;
    }

    /**
     * Remove message
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return void
     * @throws Exception
     */
    public function removeMessage($id)
    {
        // check if this message identifier exists
        if (!isset ($this->messages [$id])) {
            throw new Exception ($this,
                ExceptionCode::MESSAGE_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // remove message
        unset ($this->messages [$id]);
    }

    /**
     * Check permissions to display message
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return bool Permission to display this message are correct (true) or not (false)
     * @throws Exception
     */
    public function checkMessagePermissions($id)
    {
        // check if message exists
        if (!isset ($this->messages [$id])) {
            throw new Exception ($this,
                ExceptionCode::MESSAGE_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // check permissions to display message
        if (isset ($this->messages [$id] ['permissions'])) {
            return Helper::getInstance()->checkCurrentPermissions($this->messages [$id] ['permissions']);
        }
        // exit
        return true;
    }

    /**
     * Check if message will be displayed
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return bool Message will be displayed (true) or not (false)
     * @throws Exception
     */
    public function checkMessageToDisplay($id)
    {
        // check if message exists
        if (!isset ($this->messages [$id])) {
            throw new Exception ($this,
                ExceptionCode::MESSAGE_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // check if message will be displayed
        $output = true;
        if (!((isset ($this->messages [$id] ['allpages'])) && ($this->messages [$id] ['allpages']))) {
            $output = (empty ($this->messages [$id] ['pages'])) ? $this->getComponent('settings-menu',
                ProjectCategory::BACKEND)->checkCurrentPageIsSettingsForProject() : in_array($this->getComponent('settings-menu',
                ProjectCategory::BACKEND)->getCurrentSettingsMenu(),
                $this->messages [$id] ['pages']);
        }
        // exit
        return $output ? (($this->checkMessagePermissions($id)) && (!$this->checkMessageClosed($id))) : false;
    }

    /**
     * Close message
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return void
     * @throws Exception
     */
    public function closeMessage($id)
    {
        // check if message exists
        if (!isset ($this->messages [$id])) {
            throw new Exception ($this,
                ExceptionCode::MESSAGE_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // close message
        $this->getComponent('meta')->addOrUpdate(self::getOptionNameMessageClosed() . '__' . $id, '1');
    }

    /**
     * Restore closed message
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return void
     * @throws Exception
     */
    public function restoreClosedMessage($id)
    {
        // check if message exists
        if (!isset ($this->messages [$id])) {
            throw new Exception ($this,
                ExceptionCode::MESSAGE_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // restore closed message
        $this->getComponent('meta')->delete(self::getOptionNameMessageClosed() . '__' . $id);
    }

    /**
     * Check if message is closed
     *
     * @access public
     * @param string $id
     *            Message identifier
     * @return bool Message is closed (true) or not (false)
     */
    public function checkMessageClosed($id)
    {
        // exit
        return isset ($this->messages [$id]) ? ($this->getComponent('meta')->get(self::getOptionNameMessageClosed() . '__' . $id) === '1') : false;
    }

    /**
     * Action for admin header
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function actionAdminHead()
    {
        // check if add script
        $display = false;
        foreach ($this->messages as $id => $data) {
            if (($this->checkMessageToDisplay($id)) && (isset ($data ['closable']))) {
                $display = true;
            }
        }
        // add script
        if ($display) {
            $this->getComponent('js-ajax', ProjectCategory::ALL)->addAjaxJs();
            JsHelper::getInstance()->addLibScript('backend-message',
                'project/components/backend/message', 'message', array(
                    'helper'
                ), array(
                    Helper::getInstance()->getPrefix() . '-all-js-ajax'
                ), 'kocujILV13aBackendMessageVals', array(
                    'prefix' => Helper::getInstance()->getPrefix(),
                    'security' => wp_create_nonce(Helper::getInstance()->getPrefix() . '__message')
                ));
        }
    }

    /**
     * Action for admin notices
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function actionAdminNotices()
    {
        // show messages
        foreach ($this->messages as $id => $data) {
            // show message
            if ($this->checkMessageToDisplay($id)) {
                // get message class
                $addClass = array(
                    Type::INFORMATION => 'notice-info',
                    Type::WARNING => 'notice-warning',
                    Type::ERROR => 'notice-error',
                    Type::SUCCESS => 'notice-success'
                );
                $type = (isset ($data ['type'])) ? $data ['type'] : Type::INFORMATION;
                $class = 'notice' . ((isset ($addClass [$type])) ? ' ' . $addClass [$type] : '');
                if (isset ($data ['closable'])) {
                    $class .= ' is-dismissible';
                }
                // prepare style
                $style = (isset ($data ['style'])) ? ' style="' . esc_attr($data ['style']) . '"' : '';
                // prepare additional class
                if (isset ($data ['class'])) {
                    $class .= ' ' . $data ['class'];
                }
                // show message
                echo '<div' . $style . ' id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__message_' . $id) . '" class="' . esc_attr($class) . '"><p>' . $data ['content'] . '</p></div>';
                // add or change message transient data for close message
                if ((isset ($data ['closable'])) && ($data ['closable'] === Closable::CLOSABLE)) {
                    DbDataHelper::getInstance()->mergeTransientArray('kocuj_il_' . Version::getInstance()->getVersionInternal() . '_messages_close',
                        $id, array(
                            'closecallback' => (isset ($data ['closecallback'])) ? $data ['closecallback'] : ''
                        ), 3600);
                }
            }
        }
    }

    /**
     * Action for admin footer scripts
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function actionPrintFooterScripts()
    {
        // show scripts
        foreach ($this->messages as $id => $data) {
            // show script for closing window
            if (($this->checkMessageToDisplay($id)) && (isset ($data ['closable']))) {
                ?>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    (function ($) {
                        $(document).ready(function () {
                            kocujILV13aBackendMessage.addProjectIfNotExists('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>');
                            kocujILV13aBackendMessage.addMessageCloseButton('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($id); ?>');
                        });
                    }(jQuery));
                    /* ]]> */
                </script>
                <?php
            }
        }
    }
}
