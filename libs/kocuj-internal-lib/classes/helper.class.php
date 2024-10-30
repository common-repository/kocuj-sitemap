<?php

/**
 * helper.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a\Enums\CheckJavascript;
use KocujIL\V13a\Enums\LogicOperator;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Helper class
 *
 * @access public
 */
final class Helper
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Script URL is remembered in $this->scriptUrl property (true) or not and should be updated (false)
     *
     * @access private
     * @var bool
     */
    private $scriptUrlRemember = false;

    /**
     * Remembered script URL
     *
     * @access private
     * @var string
     */
    private $scriptUrl = '';

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
     * Get prefix for some names in library
     *
     * @access public
     * @return string Prefix
     */
    public function getPrefix()
    {
        // exit
        return 'kocujil' . Version::getInstance()->getVersionInternal();
    }

    /**
     * Check if it is debug mode
     *
     * @access public
     * @param int $checkJavascript
     *            Check JavaScript; must be one of the following constants from \KocujIL\V13a\Enums\CheckJavascript: NO (when JavaScript should not be checked) or YES (when JavaScript should be checked) - default: \KocujIL\V13a\Enums\CheckJavascript::NO
     * @return bool It is debug mode (true) or not (false)
     */
    public function checkDebug($checkJavascript = CheckJavascript::NO)
    {
        // exit
        return (((defined('WP_DEBUG')) && (WP_DEBUG)) || (((defined('SCRIPT_DEBUG')) && (SCRIPT_DEBUG)) || ($checkJavascript === CheckJavascript::NO)));
    }

    /**
     * Calculate the highest priority of WordPress filter or action
     *
     * @param string $name
     *            WordPress filter or action name
     * @return int Calculated highest priority of WordPress filter or action
     * @todo remove checking if $wp_filter is an array, when this library will have 4.7 as the minimal supported WordPress version
     * @access public
     */
    public function calculateMaxPriority($name)
    {
        // calculate new priority
        global $wp_filter;
        $newPriority = 1;
        if (isset ($wp_filter [$name])) {
            if (is_array($wp_filter [$name])) {
                $newPriority = (!empty ($wp_filter [$name])) ? max(array_keys($wp_filter [$name])) + 1 : 1;
            } else {
                $newPriority = ((isset ($wp_filter [$name]->callbacks)) && (!empty ($wp_filter [$name]->callbacks))) ? max(array_keys($wp_filter [$name]->callbacks)) + 1 : 1;
            }
        }
        // exit
        return ($newPriority < 9999) ? 9999 : $newPriority;
    }

    /**
     * Get better random value
     *
     * @access public
     * @param int $min
     *            Minimal value
     * @param int $max
     *            Maximum value
     * @return int Random value
     */
    public function getBetterRandom($min, $max)
    {
        // get random value
        if (function_exists('random_int')) {
            try {
                return call_user_func_array('random_int', array(
                    $min,
                    $max
                ));
            } catch (\Exception $e) {
            }
        }
        // exit
        return mt_rand($min, $max);
    }

    /**
     * Get real URL; it is similar to "realpath()" but it parse URL-s
     *
     * @access public
     * @param string $url
     *            URL to parse
     * @return string Parsed URL
     */
    public function getRealUrl($url)
    {
        // get real URL
        $url = explode('/', $url);
        $keys = array_keys($url, '..');
        foreach ($keys as $keyPos => $key) {
            array_splice($url, $key - ($keyPos * 2 + 1), 2);
        }
        // exit
        return str_replace('./', '', implode('/', $url));
    }

    /**
     * Check if IP address is in local network
     *
     * @access public
     * @param string $ip
     *            IP to check
     * @return bool IP is in local network (true) or not (false)
     */
    public function checkIPLocal($ip)
    {
        // set list of IP prefixes in local network
        $localIP = array(
            '10.',
            '172.16.',
            '172.17.',
            '172.18.',
            '172.19.',
            '172.20.',
            '172.21.',
            '172.22.',
            '172.23.',
            '172.24.',
            '172.25.',
            '172.26.',
            '172.27.',
            '172.28.',
            '172.29.',
            '172.30.',
            '172.31.',
            '192.168.'
        );
        // check if IP is in local network
        foreach ($localIP as $oneIP) {
            if (substr($ip, 0, strlen($oneIP)) === $oneIP) {
                return true;
            }
        }
        // exit
        return false;
    }

    /**
     * Check if user IP address is in local network
     *
     * @access public
     * @return bool User IP is in local network (true) or not (false)
     */
    public function checkUserIPLocal()
    {
        // exit
        return $this->checkIPLocal((isset ($_SERVER ['REMOTE_ADDR'])) ? $_SERVER ['REMOTE_ADDR'] : false);
    }

    /**
     * Check if server IP address is in local network
     *
     * @access public
     * @return bool Server IP is in local network (true) or not (false)
     */
    public function checkServerIPLocal()
    {
        // exit
        return $this->checkIPLocal((isset ($_SERVER ['SERVER_ADDR'])) ? $_SERVER ['SERVER_ADDR'] : false);
    }

    /**
     * Get protocol for current URL
     *
     * @access public
     * @return string Protocol for current URL
     */
    public function getUrlProtocol()
    {
        // check if forwarded
        if ((isset ($_SERVER ['HTTP_X_FORWARDED_PROTO']) && $_SERVER ['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $_SERVER ['HTTPS'] = 'on';
        }
        // exit
        return (((isset ($_SERVER ['HTTPS'])) && ($_SERVER ['HTTPS'] !== 'off')) || ((isset ($_SERVER ['SERVER_PORT'])) && ($_SERVER ['SERVER_PORT'] === '443'))) ? 'https' : 'http';
    }

    /**
     * Get current url
     *
     * @access public
     * @return string Current URL
     */
    public function getCurrentUrl()
    {
        // exit
        if ((isset ($_SERVER ['HTTP_HOST'])) && (isset ($_SERVER ['REQUEST_URI']))) {
            $div = explode('?', $_SERVER ['REQUEST_URI']);
            return $this->getUrlProtocol() . '://' . $_SERVER ['HTTP_HOST'] . $div [0];
        } else {
            return '';
        }
    }

    /**
     * Get script URL
     *
     * @access public
     * @return string Script URL
     */
    public function getScriptUrl()
    {
        // optionally return remembered script URL
        if ($this->scriptUrlRemember) {
            return $this->scriptUrl;
        }
        // get script URL
        if ((isset ($_SERVER ['SCRIPT_URL'])) && (isset ($_SERVER ['SCRIPT_URL'] [0]) /* strlen($_SERVER['SCRIPT_URL']) > 0 */)) {
            $scriptURL = $_SERVER ['SCRIPT_URL'];
        } else {
            if ((isset ($_SERVER ['REDIRECT_URL'])) && (isset ($_SERVER ['REDIRECT_URL'] [0]) /* strlen($_SERVER['REDIRECT_URL']) > 0 */)) {
                $scriptURL = $_SERVER ['REDIRECT_URL'];
            } else {
                if ((isset ($_SERVER ['REQUEST_URI'])) && (isset ($_SERVER ['REQUEST_URI'] [0]) /* strlen($_SERVER['REQUEST_URI']) > 0 */)) {
                    $path = parse_url($_SERVER ['REQUEST_URI']);
                    $scriptURL = $path ['path'];
                } else {
                    $scriptURL = '';
                }
            }
        }
        // remember script URL
        if (!$this->scriptUrlRemember) {
            $this->scriptUrl = $scriptURL;
            $this->scriptUrlRemember = true;
        }
        // exit
        return $scriptURL;
    }

    /**
     * Check current permissions
     *
     * @access public
     * @param array $permissions
     *            Permissions to check
     * @param int $logicOperator
     *            Logic operator; must be one of the following constants from \KocujIL\V13a\Enums\LogicOperator: OPERATOR_OR ("OR" operator) or OPERATOR_AND ("AND" operator) - default: \KocujIL\V13a\Enums\LogicOperator::OPERATOR_OR
     * @return bool Permissions are correct (true) or not (false)
     */
    public function checkCurrentPermissions(
        array $permissions,
        $logicOperator = LogicOperator::OPERATOR_OR
    ) {
        // initialize
        $output = false;
        // check current permissions
        foreach ($permissions as $permission) {
            $output = ($logicOperator === LogicOperator::OPERATOR_AND);
            switch ($logicOperator) {
                case LogicOperator::OPERATOR_OR :
                    if (current_user_can($permission)) {
                        $output = true;
                    }
                    break;
                case LogicOperator::OPERATOR_AND :
                    if (!current_user_can($permission)) {
                        $output = false;
                    }
                    break;
            }
        }
        // exit
        return $output;
    }
}
