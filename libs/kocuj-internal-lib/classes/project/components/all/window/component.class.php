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
namespace KocujIL\V13a\Classes\Project\Components\All\Window;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Enums\Project\Components\All\Window\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\All\Window\Type;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Configuration class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Windows
     *
     * @access private
     * @var array
     */
    private $windows = array();

    /**
     * Add window
     *
     * @access public
     * @param string $id
     *            Window identifier; must be unique
     * @param string $title
     *            Window title
     * @param int $width
     *            Window width
     * @param int $height
     *            Window height
     * @param int $type
     *            Window type; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\All\Window\Type: STANDARD (for standard content) or AJAX (for AJAX content) - default: \KocujIL\V13a\Enums\Project\Components\All\Window\Type::STANDARD
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "ajaxdata" (array type; data for AJAX request), "content" (string type; content for standard window; it works only if window type is set to \KocujIL\V13a\Enums\Project\Components\All\Window\Type::STANDARD), "contentcss" (array type; CSS styles for content), "url" (string type; URL for AJAX request; it works only if window type is set to \KocujIL\V13a\Enums\Project\Components\All\Window\Type::AJAX) - default: empty
     * @return void
     * @throws Exception
     */
    public function addWindow(
        $id,
        $title,
        $width,
        $height,
        $type = Type::STANDARD,
        array $attr = array()
    ) {
        // check if window identifier does not exist already
        if (isset ($this->windows [$id])) {
            throw new Exception ($this, ExceptionCode::WINDOW_ID_EXISTS, __FILE__, __LINE__, $id);
        }
        // add script
        if (empty ($this->windows)) {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-effects-fade');
            JsHelper::getInstance()->addLibVendorScript('jquery-ui-dialog-extended',
                'project/components/all/window/jQuery-UI-Dialog-extended', 'jquery.dialogOptions', array(), array(
                    'jquery-ui-dialog',
                    'jquery-effects-fade'
                ));
            $this->getComponent('js-ajax', ProjectCategory::ALL)->addAjaxJs();
            JsHelper::getInstance()->addLibScript('all-window', 'project/components/all/window',
                'window', array(
                    'helper'
                ), array(
                    Helper::getInstance()->getPrefix() . '-jquery-ui-dialog-extended',
                    Helper::getInstance()->getPrefix() . '-all-js-ajax'
                ), 'kocujILV13aAllWindowVals', array(
                    'prefix' => Helper::getInstance()->getPrefix(),
                    'dialogCssUrl' => includes_url('css/jquery-ui-dialog.min.css') . '?ver=' . get_bloginfo('version'),
                    'textLoading' => $this->getStrings('window',
                        ProjectCategory::ALL)->getString('ADD_WINDOW_SCRIPT_LOADING'),
                    'textLoadingError' => $this->getStrings('window',
                        ProjectCategory::ALL)->getString('ADD_WINDOW_SCRIPT_LOADING_ERROR')
                ));
        }
        // add window
        $this->windows [$id] = array(
            'title' => $title,
            'width' => $width,
            'height' => $height
        );
        if ($type !== Type::STANDARD) {
            $this->windows [$id] ['type'] = $type;
        }
        if ($type === Type::STANDARD) {
            $this->windows [$id] ['content'] = $attr ['content'];
        }
        if ($type === Type::AJAX) {
            $this->windows [$id] ['url'] = $attr ['url'];
            $this->windows [$id] ['ajaxdata'] = $attr ['ajaxdata'];
        }
        if (isset ($attr ['contentcss'])) {
            $this->windows [$id] ['contentcss'] = $attr ['contentcss'];
        }
    }

    /**
     * Get windows data
     *
     * @access public
     * @return array Windows data; each window data has the following fields: "ajaxdata" (data for AJAX request), "content" (content for standard window), "contentcss" (CSS styles for content), "height" (window height), "type" (type of window), "url" (URL for AJAX request), "width" (window width)
     */
    public function getWindows()
    {
        // prepare windows
        $windows = $this->windows;
        foreach ($windows as $key => $val) {
            if (!isset ($val ['ajaxdata'])) {
                $windows [$key] ['ajaxdata'] = array();
            }
            if (!isset ($val ['content'])) {
                $windows [$key] ['content'] = '';
            }
            if (!isset ($val ['contentcss'])) {
                $windows [$key] ['contentcss'] = array();
            }
            if (!isset ($val ['type'])) {
                $windows [$key] ['type'] = Type::STANDARD;
            }
            if (!isset ($val ['url'])) {
                $windows [$key] ['url'] = '';
            }
        }
        // exit
        return $windows;
    }

    /**
     * Check if window exists
     *
     * @access public
     * @param string $id
     *            Window identifier
     * @return bool Window exists (true) or not (false)
     */
    public function checkWindow($id)
    {
        // exit
        return isset ($this->windows [$id]);
    }

    /**
     * Get window data by id
     *
     * @access public
     * @param string $id
     *            Window identifier
     * @return array|bool Window data or false if not exists; window data have the following fields: "ajaxdata" (data for AJAX request), "content" (content for standard window), "contentcss" (CSS styles for content), "height" (window height), "type" (type of window), "url" (URL for AJAX request), "width" (window width)
     */
    public function getWindow($id)
    {
        // get windows
        $windows = $this->getWindows();
        // exit
        return (isset ($windows [$id])) ? $windows [$id] : false;
    }

    /**
     * Remove window
     *
     * @access public
     * @param string $id
     *            Window identifier
     * @return void
     * @throws Exception
     */
    public function removeWindow($id)
    {
        // check if this window identifier exists
        if (!isset ($this->windows [$id])) {
            throw new Exception ($this,
                ExceptionCode::WINDOW_ID_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // remove window
        unset ($this->windows [$id]);
    }

    /**
     * Get window JavaScript code
     *
     * @access public
     * @param string $id
     *            Window identifier
     * @return string Window JavaScript code
     * @throws Exception
     */
    public function getWindowJsCode($id)
    {
        // check if this window identifier exists
        if (!isset ($this->windows [$id])) {
            throw new Exception ($this,
                ExceptionCode::WINDOW_ID_DOES_NOT_EXIST, __FILE__,
                __LINE__, $id);
        }
        // get window data
        $window = $this->getWindow($id);
        // get window type
        $type = 'STANDARD';
        switch ($window ['type']) {
            case Type::AJAX :
                $type = 'AJAX';
                break;
        }
        // get JavaScript attributes object
        $attrObj = '\'title\':\'' . $window ['title'] . '\',\'width\':' . $window ['width'] . ',\'height\':' . $window ['height'] . ',';
        $contentCss = '';
        if (!empty ($window ['contentcss'])) {
            foreach ($window ['contentcss'] as $key => $val) {
                $contentCss .= '\'' . esc_js($key) . '\':\'' . esc_js($val) . '\',';
            }
            $contentCss = substr($contentCss, 0, -1);
        }
        $attrObj .= '\'contentCss\':{' . $contentCss . '},';
        if ($window ['type'] === Type::STANDARD) {
            $attrObj .= '\'content\':\'' . esc_js($window ['content']) . '\',';
        }
        if ($window ['type'] === Type::AJAX) {
            $attrObj .= '\'url\':\'' . esc_js($window ['url']) . '\',';
            $ajaxData = '';
            if (!empty ($window ['ajaxdata'])) {
                foreach ($window ['ajaxdata'] as $key => $val) {
                    $ajaxData .= '\'' . esc_js($key) . '\':\'' . esc_js($val) . '\',';
                }
                $ajaxData = substr($ajaxData, 0, -1);
            }
            $attrObj .= '\'ajaxData\':{' . $ajaxData . '},';
        }
        $attrObj = substr($attrObj, 0, -1);
        // exit
        return 'kocujILV13aAllWindow.show(\'' . esc_js($this->getProjectObj()->getMainSettingInternalName()) . '\', \'' . esc_js($id) . '\', kocujILV13aAllWindowType.' . $type . ', {' . $attrObj . '});';
    }

    /**
     * Get window JavaScript function with code
     *
     * @access public
     * @param string $id
     *            Window identifier
     * @param
     *            string &$functionName Returned JavaScript function name
     * @param string $forceSuffix
     *            Force JavaScript function suffix - default: empty
     * @return string Window JavaScript function with code
     * @throws Exception
     */
    public function getWindowJsFunction($id, &$functionName, $forceSuffix = '')
    {
        // initialize
        $functionName = $this->getComponent('project-helper')->getPrefix() . '__window_function__' . $id . $forceSuffix;
        // exit
        return 'if (typeof ' . $functionName . ' !== \'function\') {' . PHP_EOL . 'function ' . $functionName . '() {' . PHP_EOL . $this->getWindowJsCode($id) . PHP_EOL . '}' . PHP_EOL . '}' . PHP_EOL;
    }

    /**
     * Action for admin footer scripts
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // show scripts
        if (!empty ($this->windows)) {
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function ($) {
                    $(document).ready(function () {
                        kocujILV13aAllWindow.addProject('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>');
                    });
                }(jQuery));
                /* ]]> */
            </script>
            <?php
        }
    }
}
