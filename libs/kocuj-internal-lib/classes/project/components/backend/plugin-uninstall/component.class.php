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
namespace KocujIL\V13a\Classes\Project\Components\Backend\PluginUninstall;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Enums\Project\Components\Backend\PluginUninstall\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\Core\Meta\Type;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Plugin uninstallation class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Constructor
     *
     * @access public
     * @param object $projectObj
     *            \KocujIL\V13a\Classes\Project object for current project
     * @throws Exception
     */
    public function __construct($projectObj)
    {
        // execute parent constructor
        parent::__construct($projectObj);
        // check if it is plugin
        if ($this->getProjectObj()->getMainSettingType() !== ProjectType::PLUGIN) {
            throw new Exception ($this, ExceptionCode::WRONG_PROJECT_TYPE, __FILE__, __LINE__);
        }
    }

    /**
     * Plugin uninstallation
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function uninstall()
    {
        // check if it is uninstallation of plugin
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            throw new Exception ($this, ExceptionCode::CANNOT_UNINSTALL_PLUGIN, __FILE__, __LINE__);
        }
        // uninstall plugin
        $this->getComponent('project-helper')->doAction('plugin_uninstall');
        // remove meta options
        $this->getComponent('meta')->forceRealClearDataNow();
        $this->getComponent('meta')->forceRealClearDataNow(Type::SITE);
        // get container and option identifier
        $containerId = $this->getComponent('page-uninstall', ProjectCategory::BACKEND)->getOptionsContainerId();
        $optionId = $this->getComponent('page-uninstall', ProjectCategory::BACKEND)->getOptionId();
        // optionally remove administration data from database
        $removeOptions = ((isset ($containerId [0]) /* strlen($containerId) > 0 */) && (isset ($optionId [0]) /* strlen($optionId) > 0 */)) ? ($this->getComponent('options',
                ProjectCategory::ALL)->getOption($containerId, $optionId) === '1') : true;
        if ($removeOptions) {
            $this->getComponent('options', ProjectCategory::ALL)->removeAllContainersFromDb();
        }
        if (is_multisite()) {
            global $wpdb;
            $ids = $wpdb->get_col('SELECT blog_id FROM ' . $wpdb->blogs);
            if (!empty ($ids)) {
                $currentId = get_current_blog_id();
                foreach ($ids as $id) {
                    if ($id !== $currentId) {
                        switch_to_blog($id);
                        if ($removeOptions) {
                            $this->getComponent('options', ProjectCategory::ALL)->removeAllContainersFromDb();
                        }
                        $this->getComponent('meta')->forceRealClearDataNow(Type::SITE);
                        restore_current_blog();
                    }
                }
            }
        }
    }
}
