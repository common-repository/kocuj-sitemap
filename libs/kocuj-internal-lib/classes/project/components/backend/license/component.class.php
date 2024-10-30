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
namespace KocujIL\V13a\Classes\Project\Components\Backend\License;

// set namespace
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\HtmlHelper;
use KocujIL\V13a\Enums\Project\Components\All\Window\Type;
use KocujIL\V13a\Enums\Project\Components\Backend\License\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * License class
 *
 * @access public
 */
class Component extends ComponentObject
{
    /**
     * Get license filename
     *
     * @access public
     * @return string License filename; if license file does not exist, it returns empty string
     * @throws Exception
     */
    public function getLicenseFilename()
    {
        // get license filename
        $language = get_locale();
        if ((isset ($language [0]) /* strlen($language) > 0 */) && (is_file($this->getComponent('dirs')->getProjectDir() . DIRECTORY_SEPARATOR . 'license-' . $language . '.txt'))) {
            $licenseFilename = 'license-' . $language . '.txt';
        } else {
            $licenseFilename = 'license.txt';
            if (!is_file($this->getComponent('dirs')->getProjectDir() . DIRECTORY_SEPARATOR . $licenseFilename)) {
                throw new Exception ($this, ExceptionCode::LICENSE_FILE_DOES_NOT_EXIST, __FILE__, __LINE__);
            }
        }
        // exit
        return $licenseFilename;
    }

    /**
     * Get license script
     *
     * @access private
     * @return string License script
     */
    private function getLicenseScript()
    {
        // optionally add window
        $value = $this->getComponent('window', ProjectCategory::ALL)->checkWindow('license');
        if (!$value) {
            $this->getComponent('window', ProjectCategory::ALL)->addWindow('license',
                $this->getStrings('license', ProjectCategory::BACKEND)->getString('GET_LICENSE_SCRIPT_LICENSE_TITLE'),
                400, 400, Type::AJAX, array(
                    'url' => admin_url('admin-ajax.php'),
                    'ajaxdata' => array(
                        'action' => $this->getComponent('project-helper')->getPrefix() . '__license_display',
                        'security' => wp_create_nonce(Helper::getInstance()->getPrefix() . '__license')
                    ),
                    'contentcss' => array(
                        'font-family' => '"Courier New", Courier, monospace',
                        'text-align' => 'center'
                    )
                ));
        }
        // exit
        return $this->getComponent('window', ProjectCategory::ALL)->getWindowJsCode('license');
    }

    /**
     * Get license link or just name if license file does not exist
     *
     * @access public
     * @return string License link or just name if license file does not exist
     * @throws Exception
     */
    public function getLicenseLink()
    {
        // get license link or name
        $licenseFilename = $this->getLicenseFilename();
        if (isset ($licenseFilename [0]) /* strlen($licenseFilename) > 0 */) {
            // set HTML identifier
            $id = $this->getComponent('project-helper')->getPrefix() . '__licenselink_' . $this->getProjectObj()->getMainSettingInternalName() . '_' . rand(111111,
                    999999);
            // exit
            return HtmlHelper::getInstance()->getLink('#',
                    $this->getProjectObj()->getMainSettingLicenseName(), array(
                        'id' => $id,
                        'styleclassfilter' => array(
                            'projectobj' => $this->getProjectObj(),
                            'filter' => 'license_link'
                        )
                    )) . '<script type="text/javascript">' . PHP_EOL . '/* <![CDATA[ */' . PHP_EOL . '(function($) {' . PHP_EOL . '$(document).ready(function() {' . PHP_EOL . '$(\'' . esc_js('#' . $id) . '\').attr(\'href\', \'javascript:void(0);\');' . PHP_EOL . '$(\'' . esc_js('#' . $id) . '\').click(function(event) {' . PHP_EOL . 'event.preventDefault();' . PHP_EOL . $this->getLicenseScript() . PHP_EOL . '});' . PHP_EOL . '});' . PHP_EOL . '}(jQuery));' . PHP_EOL . '/* ]]> */' . PHP_EOL . '</script>' . PHP_EOL;
        } else {
            // exit
            return $this->getProjectObj()->getMainSettingLicenseName();
        }
    }
}
