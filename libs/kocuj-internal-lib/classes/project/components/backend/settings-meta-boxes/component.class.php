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
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsMetaBoxes;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsMetaBoxes\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Meta boxes for settings class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Settings meta boxes data
     *
     * @access private
     * @var array
     */
    private $settingsMetaBoxes = array();

    /**
     * Add settings meta box
     *
     * @access public
     * @param string $id
     *            Settings meta box id; must be unique in this project
     * @param string $title
     *            Settings meta box title
     * @param string $content
     *            Settings meta box content
     * @param array $pages
     *            Settings pages list for current project where settings meta box should be displayed; it should be identifiers from settings page for current project only; if it is empty, meta box will be displayed on all settings pages for current project - default: empty
     * @return void
     * @throws Exception
     */
    public function addSettingsMetaBox($id, $title, $content, array $pages = array())
    {
        // check if settings meta box does not exist already
        if (isset ($this->settingsMetaBoxes [$id])) {
            throw new Exception ($this, ExceptionCode::SETTINGS_META_BOX_ID_EXISTS, __FILE__, __LINE__, $id);
        }
        // add settings meta box
        $this->settingsMetaBoxes [$id] = array(
            'title' => $title,
            'content' => $content
        );
        if (!empty ($pages)) {
            $this->settingsMetaBoxes [$id] ['pages'] = $pages;
        }
    }

    /**
     * Get settings meta boxes data
     *
     * @access public
     * @return array Settings meta boxes data; each settings meta box data has the following fields: "pages" (settings pages list for current project on which meta box is displayed or empty if it is displayed on all settings pages for current project), "title" (title of meta box), "content" (content of meta box)
     */
    public function getSettingsMetaBoxes()
    {
        // prepare settings meta boxes
        $settingsMetaBoxes = $this->settingsMetaBoxes;
        foreach ($settingsMetaBoxes as $key => $val) {
            if (!isset ($val ['pages'])) {
                $settingsMetaBoxes [$key] ['pages'] = array();
            }
        }
        // exit
        return $settingsMetaBoxes;
    }

    /**
     * Check if settings meta box exists
     *
     * @access public
     * @param string $id
     *            Settings meta box identifier
     * @return bool Settings meta box exists (true) or not (false)
     */
    public function checkSettingsMetaBox($id)
    {
        // exit
        return isset ($this->settingsMetaBoxes [$id]);
    }

    /**
     * Get settings meta box data by id
     *
     * @access public
     * @param string $id
     *            Settings meta box identifier
     * @return array|bool Selected settings meta box data or false if not exists; meta box data have the following fields: "pages" (settings pages list for current project on which meta box is displayed or empty if it is displayed on all settings pages for current project), "title" (title of meta box), "content" (content of meta box)
     */
    public function getSettingsMetaBox($id)
    {
        // get settings meta boxes
        $settingsMetaBoxes = $this->getSettingsMetaBoxes();
        // exit
        return (isset ($settingsMetaBoxes [$id])) ? $settingsMetaBoxes [$id] : false;
    }

    /**
     * Remove settings meta box
     *
     * @access public
     * @param string $id
     *            Settings meta box identifier
     * @return void
     * @throws Exception
     */
    public function removeSettingsMetaBox($id)
    {
        // check if this settings meta box identifier exists
        if (!isset ($this->messages [$id])) {
            throw new Exception ($this, ExceptionCode::SETTINGS_META_BOX_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $id);
        }
        // remove settings meta box
        unset ($this->settingsMetaBoxes [$id]);
    }

    /**
     * Check if settings meta box with the selected identifier should be displayed on current page
     *
     * @access private
     * @param string $id
     *            Settings meta box identifier to check
     * @return bool There should be displayed settings meta box on current page (true) or not (false)
     */
    private function checkIfDisplaySettingsMetaBox($id)
    {
        // check if settings meta box exists
        if (!isset ($this->settingsMetaBoxes [$id])) {
            return false;
        }
        // exit
        return (!isset ($this->settingsMetaBoxes [$id] ['pages'])) ? $this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->checkCurrentPageIsSettingsForProject() : in_array($this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->getCurrentSettingsMenu(), $this->settingsMetaBoxes [$id] ['pages']);
    }

    /**
     * Check if any settings meta box should be displayed on current page
     *
     * @access private
     * @return bool There should be displayed settings meta box on current page (true) or not (false)
     */
    private function checkIfDisplayAnySettingsMetaBox()
    {
        // check if there are any settings meta boxes
        if (empty ($this->settingsMetaBoxes)) {
            return false;
        }
        // check each settings meta box
        foreach ($this->settingsMetaBoxes as $key => $val) {
            if ($this->checkIfDisplaySettingsMetaBox($key)) {
                return true;
            }
        }
        // exit
        return false;
    }

    /**
     * Action for adding JavaScript scripts
     *
     * @access public
     * @return void
     */
    public function actionEnqueueScripts()
    {
        // check if scripts should be added
        if (!$this->checkIfDisplayAnySettingsMetaBox()) {
            return;
        }
        // add scripts
        wp_enqueue_script('post');
    }

    /**
     * Action before form div element
     *
     * @access public
     * @return void
     */
    public function actionBeforeFormDiv()
    {
        // check if styles should be added
        if (!$this->checkIfDisplayAnySettingsMetaBox()) {
            return;
        }
        // add styles
        echo '<style scoped="scoped" type="text/css" media="all">' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__div_wrapinside {' . PHP_EOL . 'float: left;' . PHP_EOL . 'width: 60%;' . PHP_EOL . 'width: -moz-calc(100% - 300px);' . PHP_EOL . 'width: -webkit-calc(100% - 300px);' . PHP_EOL . 'width: -o-calc(100% - 300px);' . PHP_EOL . 'width: calc(100% - 300px);' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__div_settings_meta_boxes_before {' . PHP_EOL . 'display: none;' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__div_settings_meta_boxes {' . PHP_EOL . 'float: right !important;' . PHP_EOL . 'width: 280px !important;' . PHP_EOL . '}' . PHP_EOL . '@media screen and (max-width: 782px) {' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__div_wrapinside {' . PHP_EOL . 'float: none;' . PHP_EOL . 'width: 100%;' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__div_settings_meta_boxes_before {' . PHP_EOL . 'display: block;' . PHP_EOL . '}' . PHP_EOL . '#' . $this->getComponent('project-helper')->getPrefix() . '__div_settings_meta_boxes {' . PHP_EOL . 'float: none !important;' . PHP_EOL . 'width: 100% !important;' . PHP_EOL . 'margin-top: 20px !important;' . PHP_EOL . '}' . PHP_EOL . '}' . PHP_EOL . '</style>' . PHP_EOL;
    }

    /**
     * Action after form div element
     *
     * @access public
     * @return void
     */
    public function actionAfterFormDiv()
    {
        // check if scripts should be added
        if (!$this->checkIfDisplayAnySettingsMetaBox()) {
            return;
        }
        // show settings meta boxes
        ?>
        <div
                id="<?php echo esc_attr($this->getComponent('project-helper')->getPrefix() . '__div_settings_meta_boxes_before'); ?>">
            <br/> <br/>
            <hr/>
            <br/>
        </div>
        <div id="poststuff" style="min-width: 0;">
            <div id="post-body">
                <div
                        id="<?php echo esc_attr($this->getComponent('project-helper')->getPrefix() . '__div_settings_meta_boxes'); ?>"
                        class="postbox-container">
                    <?php
                    foreach ($this->settingsMetaBoxes as $id => $data) {
                        if ($this->checkIfDisplaySettingsMetaBox($id)) {
                            add_meta_box('kocuj_internal_lib_settings_meta_box_' . $id, $data ['title'], array(
                                $this,
                                'metaBoxShow'
                            ), 'kocuj_internal_lib', 'advanced', 'default', array(
                                'id' => $id
                            ));
                        }
                    }
                    do_meta_boxes('kocuj_internal_lib', 'advanced', null);
                    foreach ($this->settingsMetaBoxes as $id => $data) {
                        if ($this->checkIfDisplaySettingsMetaBox($id)) {
                            remove_meta_box('kocuj_internal_lib_settings_meta_box_' . $id, 'kocuj_internal_lib',
                                'advanced', 'default');
                        }
                    }
                    ?>
                    <script type="text/javascript">
                        /* <![CDATA[ */
                        (function ($) {
                            'use strict';
                            $(document).ready(function ($) {
                                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                                postboxes.add_postbox_toggles('kocujil');
                                var metaBoxSortablesElement = $('.meta-box-sortables');
                                metaBoxSortablesElement.sortable({
                                    disabled: true
                                });
                                metaBoxSortablesElement.css('margin-top', '-10px');
                                $('.postbox-container .handlediv').click(function () {
                                    var classes = $(this).parent().attr('class').split(/\s+/);
                                    var closed = false;
                                    if (classes.length > 0) {
                                        for (var z = 0; z < classes.length; z++) {
                                            if (classes[z] === 'closed') {
                                                closed = true;
                                                break;
                                            }
                                        }
                                    }
                                    if (closed) {
                                        $(this).parent().removeClass('closed');
                                    } else {
                                        $(this).parent().addClass('closed');
                                    }
                                });
                            });
                        }(jQuery));
                        /* ]]> */
                    </script>
                    <?php
                    wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
                    wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Show settings meta box
     *
     * @access public
     * @param object $post
     *            Post data; it is always set to NULL
     * @param object $data
     *            Data
     * @return void
     */
    public function metaBoxShow($post, $data)
    {
        // show settings meta box content
        $id = $data ['args'] ['id'];
        if (!isset ($this->settingsMetaBoxes [$id])) {
            return;
        }
        echo $this->settingsMetaBoxes [$id] ['content'];
    }
}
