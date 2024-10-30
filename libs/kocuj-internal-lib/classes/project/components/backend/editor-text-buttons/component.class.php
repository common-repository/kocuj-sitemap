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
namespace KocujIL\V13a\Classes\Project\Components\Backend\EditorTextButtons;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Enums\Project\Components\Backend\EditorTextButtons\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Text editor buttons class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Buttons
     *
     * @access private
     * @var array
     */
    private $buttons = array();

    /**
     * Add button for text editor
     *
     * @access public
     * @param string $id
     *            Button id; must be unique for this project
     * @param string $title
     *            Button title
     * @param string $helpText
     *            Help text for button
     * @param string $codeBegin
     *            Begin of code added after clicking on button
     * @param string $codeEnd
     *            End of code added after clicking on button
     * @param int $priority
     *            Button priority - default: empty
     * @param string $accessKey
     *            Access key - default: empty
     * @return void
     * @throws Exception
     */
    public function addButton($id, $title, $helpText, $codeBegin, $codeEnd, $priority = 0, $accessKey = '')
    {
        // check if button does not exist already
        if (isset ($this->buttons [$id])) {
            throw new Exception ($this, ExceptionCode::BUTTON_ID_EXISTS, __FILE__, __LINE__, $id);
        }
        // add button
        $this->buttons [$id] = array(
            'title' => $title,
            'helptext' => $helpText,
            'codebegin' => $codeBegin,
            'codeend' => $codeEnd
        );
        if ($priority !== 0) {
            $this->buttons [$id] ['priority'] = $priority;
        }
        if (isset ($accessKey [0]) /* strlen($accessKey) > 0 */) {
            $this->buttons [$id] ['accesskey'] = $accessKey;
        }
    }

    /**
     * Get buttons for text editor data
     *
     * @access public
     * @return array Buttons for text editor data; each button for text editor data has the following fields: "accesskey" (string type; access key), "codebegin" (string type; begin of code added after clicking on button), "codeend" (string type; end of code added after clicking on button), "helptext" (string type; help text for button), "title" (string type; button title)
     */
    public function getButtons()
    {
        // prepare buttons
        $buttons = $this->buttons;
        foreach ($buttons as $key => $val) {
            if (!isset ($val ['priority'])) {
                $buttons [$key] ['priority'] = 0;
            }
            if (!isset ($val ['accesskey'])) {
                $buttons [$key] ['accesskey'] = '';
            }
        }
        // exit
        return $buttons;
    }

    /**
     * Check if button for text editor exists
     *
     * @access public
     * @param string $id
     *            Button for text editor identifier
     * @return bool Button for text editor exists (true) or not (false)
     */
    public function checkButton($id)
    {
        // exit
        return isset ($this->buttons [$id]);
    }

    /**
     * Get button for text editor data by id
     *
     * @access public
     * @param string $id
     *            Button for text editor identifier
     * @return array|bool Button for text editor data or false if not exists; button for text editor data have the following fields: "accesskey" (string type; access key), "codebegin" (string type; begin of code added after clicking on button), "codeend" (string type; end of code added after clicking on button), "helptext" (string type; help text for button), "title" (string type; button title)
     */
    public function getButton($id)
    {
        // get buttons
        $buttons = $this->getButtons();
        // exit
        return (isset ($buttons [$id])) ? $buttons [$id] : false;
    }

    /**
     * Remove button for text editor
     *
     * @access public
     * @param string $id
     *            Button for text editor identifier
     * @return void
     * @throws Exception
     */
    public function removeButton($id)
    {
        // check if this button for text editor identifier exists
        if (!isset ($this->buttons [$id])) {
            throw new Exception ($this, ExceptionCode::BUTTON_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $id);
        }
        // remove button for text editor
        unset ($this->buttons [$id]);
    }

    /**
     * Action for show text editor buttons
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // check if Quicktags script is active
        if (!wp_script_is('quicktags')) {
            return;
        }
        // show text editor buttons
        if (!empty ($this->buttons)) {
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function ($) {
                    $(document).ready(function () {
                        <?php
                        foreach ($this->buttons as $id => $button) {
                            echo 'QTags.addButton(\'' . esc_js($this->getComponent('project-helper')->getPrefix() . '__' . $id) . '\', \'' . esc_js($button ['title']) . '\', \'' . esc_js($button ['codebegin']) . '\', \'' . esc_js($button ['codeend']) . '\', \'' . esc_js((isset ($button ['accesskey'])) ? $button ['accesskey'] : '') . '\', \'' . esc_js($button ['helptext']) . '\', \'' . esc_js((isset ($button ['priority'])) ? $button ['priority'] : '0') . '\');' . PHP_EOL;
                        }
                        ?>
                    });
                }(jQuery));
                /* ]]> */
            </script>
            <?php
        }
    }
}
