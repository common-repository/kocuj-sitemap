<?php

/**
 * db-data-helper.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a\Enums\Area;
use KocujIL\V13a\Enums\OptionAutoload;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Database data helper class
 *
 * @access public
 */
final class DbDataHelper
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Disable cloning of object
     *
     * @access private
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Get singleton instance
     *
     * @access public
     * @return object Singleton instance
     */
    public static function getInstance()
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self ();
        }
        // exit
        return self::$instance;
    }

    /**
     * Check if area is set to network
     *
     * @access private
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area)
     * @return bool Area is set to network (true) or not (false)
     */
    private function checkAreaNetwork($area)
    {
        // check if area is set to network
        $areas = array(
            Area::AUTO => is_multisite(),
            Area::SITE => false,
            Area::NETWORK => true
        );
        // exit
        return $areas [$area];
    }

    /**
     * Call function
     *
     * @access private
     * @param bool $isOption
     *            It is option (true) or transient (false)
     * @param string $prefix
     *            Function name prefix
     * @param array $pars
     *            Function parameters
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area)
     * @return array|bool|float|int|string Returned value
     */
    private function callFunction($isOption, $prefix, array $pars, $area)
    {
        // exit
        return call_user_func_array($prefix . ((($this->checkAreaNetwork($area)) && (is_multisite())) ? '_site' : '') . '_' . ($isOption ? 'option' : 'transient'),
            $pars);
    }

    /**
     * Add option or transient for network or site
     *
     * @access private
     * @param bool $isOption
     *            It is option (true) or transient (false)
     * @param string $name
     *            Option or transient name
     * @param array|bool|float|int|string $value
     *            Option or transient value
     * @param bool|int $autoloadOrExpiration
     *            For option it sets automatic loading of option (true) or not (false) and works only if option is for site, not network; for transient it is expiration in seconds
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area)
     * @return void
     */
    private function add($isOption, $name, $value, $autoloadOrExpiration, $area)
    {
        // add option or transient
        $pars = array(
            $name,
            $value
        );
        if ($isOption) {
            if ((!$this->checkAreaNetwork($area)) || (!is_multisite())) {
                $pars [] = '';
                $pars [] = $autoloadOrExpiration;
            }
        } else {
            $pars [] = $autoloadOrExpiration;
        }
        $this->callFunction($isOption, ($isOption ? 'add' : 'set'), $pars, $area);
    }

    /**
     * Delete option or transient for network or site
     *
     * @access private
     * @param bool $isOption
     *            It is option (true) or transient (false)
     * @param string $name
     *            Option or transient name
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area)
     * @return void
     */
    private function delete($isOption, $name, $area)
    {
        // delete option or transient
        $this->callFunction($isOption, 'delete', array(
            $name
        ), $area);
    }

    /**
     * Get option or transient for network or site
     *
     * @access private
     * @param bool $isOption
     *            It is option (true) or transient (false)
     * @param string $name
     *            Option or transient name
     * @param array|bool|float|int|string $defaultValue
     *            Default option or transient value
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area)
     * @return array|bool|float|int|string Option or transient value
     */
    private function get($isOption, $name, $defaultValue, $area)
    {
        // exit
        return $this->callFunction($isOption, 'get', array(
            $name,
            $defaultValue
        ), $area);
    }

    /**
     * Merge or unmerge element to option or transient array for network or site
     *
     * @access private
     * @param bool $isOption
     *            It is option (true) or transient (false)
     * @param bool $merge
     *            Merge (true) or unmerge (false)
     * @param string $name
     *            Option or transient name
     * @param string $key
     *            Option or transient element key
     * @param array|bool|float|int|string $value
     *            Option or transient element value
     * @param bool|int $autoloadOrExpiration
     *            For option it sets automatic loading of option (true) or not (false) and works only if option is for site, not network; for transient it is expiration in seconds
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area)
     * @return bool Element has been merged correctly (true) or not (false)
     */
    private function mergeOrUnmerge($isOption, $merge, $name, $key, $value, $autoloadOrExpiration, $area)
    {
        // get option or transient
        $optionOrTransientValue = $this->get($isOption, $name, false, $area);
        $optionOrTransientValue = ($optionOrTransientValue === false) ? array() : maybe_unserialize($optionOrTransientValue);
        if (!is_array($optionOrTransientValue)) {
            return false;
        }
        // add or update array element
        if ($merge) {
            $optionOrTransientValue [$key] = $value;
        } else {
            if (isset ($optionOrTransientValue [$key])) {
                unset ($optionOrTransientValue [$key]);
            }
        }
        // add or update option or transient
        $this->add($isOption, $name, $optionOrTransientValue, $autoloadOrExpiration, $area);
        // exit
        return true;
    }

    /**
     * Add option for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param array|bool|float|int|string $value
     *            Option value
     * @param int $optionAutoload
     *            Automatic loading of option or not; must be one of the following constants from \KocujIL\V13a\Enums\OptionAutoload: NO (when option should not be automatically loaded) or YES (when option should be automatically loaded) - default: \KocujIL\V13a\Enums\OptionAutoload::NO
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function addOption(
        $name,
        $value,
        $optionAutoload = OptionAutoload::NO,
        $area = Area::AUTO
    ) {
        // add option
        $this->add(true, $name, $value, $optionAutoload === OptionAutoload::YES, $area);
    }

    /**
     * Add transient for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param array|bool|float|int|string $value
     *            Transient value
     * @param int $expiration
     *            Expiration in seconds - default: 0
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function addTransient($name, $value, $expiration = 0, $area = Area::AUTO)
    {
        // add transient
        $this->add(false, $name, $value, $expiration, $area);
    }

    /**
     * Update option for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param array|bool|float|int|string $value
     *            Option value
     * @param int $optionAutoload
     *            Automatic loading of option or not; must be one of the following constants from \KocujIL\V13a\Enums\OptionAutoload: NO (when option should not be automatically loaded) or YES (when option should be automatically loaded) - default: \KocujIL\V13a\Enums\OptionAutoload::NO
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function updateOption(
        $name,
        $value,
        $optionAutoload = OptionAutoload::NO,
        $area = Area::AUTO
    ) {
        // update option
        $pars = array(
            $name,
            $value
        );
        if ((!$this->checkAreaNetwork($area)) || (!is_multisite())) {
            $pars [] = ($optionAutoload === OptionAutoload::YES);
        }
        $this->callFunction(true, 'update', $pars, $area);
    }

    /**
     * Update transient for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param array|bool|float|int|string $value
     *            Transient value
     * @param int $expiration
     *            Expiration in seconds - default: 0
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function updateTransient($name, $value, $expiration = 0, $area = Area::AUTO)
    {
        // update transient
        $this->addTransient($name, $value, $expiration, $area);
    }

    /**
     * Delete option for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function deleteOption($name, $area = Area::AUTO)
    {
        // delete option
        $this->delete(true, $name, $area);
    }

    /**
     * Delete transient for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function deleteTransient($name, $area = Area::AUTO)
    {
        // delete transient
        $this->delete(false, $name, $area);
    }

    /**
     * Get option value for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param array|bool|float|int|string $defaultOptionValue
     *            Default option value - default: false
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return array|bool|float|int|string Option value
     */
    public function getOption($name, $defaultOptionValue = false, $area = Area::AUTO)
    {
        // exit
        return $this->get(true, $name, $defaultOptionValue, $area);
    }

    /**
     * Get transient value for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param array|bool|float|int|string $defaultTransientValue
     *            Default transient value - default: false
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return array|bool|float|int|string Transient value
     */
    public function getTransient($name, $defaultTransientValue = false, $area = Area::AUTO)
    {
        // exit
        return $this->get(false, $name, $defaultTransientValue, $area);
    }

    /**
     * Add or update option for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param array|bool|float|int|string $value
     *            Option value
     * @param int $optionAutoload
     *            Automatic loading of option or not; must be one of the following constants from \KocujIL\V13a\Enums\OptionAutoload: NO (when option should not be automatically loaded) or YES (when option should be automatically loaded) - default: \KocujIL\V13a\Enums\OptionAutoload::NO
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function addOrUpdateOption(
        $name,
        $value,
        $optionAutoload = OptionAutoload::NO,
        $area = Area::AUTO
    ) {
        // check if option exists
        $exists = $this->getOption($name, false, $area);
        // add or update option
        if ($exists === false) {
            $this->addOption($name, $value, $optionAutoload, $area);
        } else {
            $this->updateOption($name, $value, $optionAutoload, $area);
        }
    }

    /**
     * Add or update transient for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param array|bool|float|int|string $value
     *            Transient value
     * @param int $expiration
     *            Expiration in seconds - default: 0
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return void
     */
    public function addOrUpdateTransient($name, $value, $expiration = 0, $area = Area::AUTO)
    {
        // add or update transient
        $this->addTransient($name, $value, $expiration, $area);
    }

    /**
     * Merge element to option array for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param string $key
     *            Option element key
     * @param array|bool|float|int|string $value
     *            Option element value
     * @param int $optionAutoload
     *            Automatic loading of option or not; must be one of the following constants from \KocujIL\V13a\Enums\OptionAutoload: NO (when option should not be automatically loaded) or YES (when option should be automatically loaded) - default: \KocujIL\V13a\Enums\OptionAutoload::NO
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return bool Element has been merged correctly (true) or not (false)
     */
    public function mergeOptionArray(
        $name,
        $key,
        $value,
        $optionAutoload = OptionAutoload::NO,
        $area = Area::AUTO
    ) {
        // exit
        return $this->mergeOrUnmerge(true, true, $name, $key, $value, $optionAutoload === OptionAutoload::YES, $area);
    }

    /**
     * Merge element to transient array for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param string $key
     *            Transient element key
     * @param array|bool|float|int|string $value
     *            Transient element value
     * @param int $expiration
     *            Expiration in seconds - default: 0
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return bool Element has been merged correctly (true) or not (false)
     */
    public function mergeTransientArray($name, $key, $value, $expiration = 0, $area = Area::AUTO)
    {
        // exit
        return $this->mergeOrUnmerge(false, true, $name, $key, $value, $expiration, $area);
    }

    /**
     * Unmerge element to option array for network or site
     *
     * @access public
     * @param string $name
     *            Option name
     * @param string $key
     *            Option element key
     * @param int $optionAutoload
     *            Automatic loading of option or not; must be one of the following constants from \KocujIL\V13a\Enums\OptionAutoload: NO (when option should not be automatically loaded) or YES (when option should be automatically loaded) - default: \KocujIL\V13a\Enums\OptionAutoload::NO
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return bool Element has been merged correctly (true) or not (false)
     */
    public function unmergeOptionArray(
        $name,
        $key,
        $optionAutoload = OptionAutoload::NO,
        $area = Area::AUTO
    ) {
        // exit
        return $this->mergeOrUnmerge(true, false, $name, $key, '', $optionAutoload === OptionAutoload::YES, $area);
    }

    /**
     * Unmerge element to transient array for network or site
     *
     * @access public
     * @param string $name
     *            Transient name
     * @param string $key
     *            Transient element key
     * @param int $expiration
     *            Expiration in seconds - default: 0
     * @param int $area
     *            Option or transient area; must be one of the following constants from \KocujIL\V13a\Enums\Area: AUTO (for automatic area), SITE (for site area) or NETWORK (for network area) - default: \KocujIL\V13a\Enums\Area::AUTO
     * @return bool Element has been merged correctly (true) or not (false)
     */
    public function unmergeTransientArray($name, $key, $expiration = 0, $area = Area::AUTO)
    {
        // exit
        return $this->mergeOrUnmerge(false, false, $name, $key, '', $expiration, $area);
    }

    /**
     * Start database transaction
     *
     * @access public
     * @return void
     */
    public function databaseTransactionStart()
    {
        // temporary disable database errors
        global $wpdb;
        $oldSuppressErrors = $wpdb->suppress_errors;
        $wpdb->suppress_errors = true;
        // start transaction
        $wpdb->query('START TRANSACTION');
        // reenable database errors
        $wpdb->suppress_errors = $oldSuppressErrors;
    }

    /**
     * End database transaction
     *
     * @access public
     * @return void
     */
    public function databaseTransactionEnd()
    {
        // temporary disable database errors
        global $wpdb;
        $oldSuppressErrors = $wpdb->suppress_errors;
        $wpdb->suppress_errors = true;
        // end transaction
        $wpdb->query('COMMIT');
        // reenable database errors
        $wpdb->suppress_errors = $oldSuppressErrors;
    }
}
