<?php

/**
 * ajax.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\All\JsAjax;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Helper;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * AJAX class
 *
 * @access public
 */
class Ajax extends ComponentObject
{

    /**
     * Action for proxy connection
     *
     * @access public
     * @return void
     */
    public function actionAjaxProxy()
    {
        // check AJAX nonce
        check_ajax_referer(Helper::getInstance()->getPrefix() . '__js_ajax', '_kocuj_internal_lib_proxy_security');
        // check if proxy is enabled
        if (ini_get('allow_url_fopen') !== '1') {
            wp_die();
        }
        // get request method
        $requestMethod = (isset ($_SERVER ['REQUEST_METHOD'])) ? $_SERVER ['REQUEST_METHOD'] : 'POST';
        if (($requestMethod !== 'GET') && ($requestMethod !== 'POST')) {
            wp_die();
        }
        // get headers
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $headers [str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        $ignoreHeaders = array(
            'Connection',
            'Cookie',
            'Host',
            'Referer',
            'X-Requested-With'
        );
        foreach ($headers as $key => $val) {
            if (in_array($key, $ignoreHeaders)) {
                unset ($headers [$key]);
            }
        }
        // get data
        $data = ($requestMethod == 'GET') ? $_GET : $_POST;
        $index = '_kocuj_internal_lib_proxy_security';
        if (isset ($data [$index])) {
            unset ($data [$index]);
        }
        $index = '_kocuj_internal_lib_proxy_old_action';
        if (isset ($data [$index])) {
            $data ['action'] = sanitize_text_field($data [$index]);
            unset ($data [$index]);
        }
        // get URL
        $index = '_kocuj_internal_lib_proxy_url';
        if (!isset ($data [$index])) {
            wp_die();
        }
        $url = sanitize_text_field($data [$index]);
        unset ($data [$index]);
        if ((substr($url, 0, 7) !== 'http://') && (substr($url, 0, 8) !== 'https://')) {
            wp_die();
        }
        // generate data for query
        $data = http_build_query($data);
        // load content
        $output = '';
        $context = @stream_context_create(array(
            'http' => array(
                'method' => $requestMethod,
                'header' => $headers,
                'content' => ($requestMethod === 'POST') ? $data : '',
                'ignore_errors' => true
            )
        ));
        if ($context !== false) {
            $output = @file_get_contents($url . (($requestMethod === 'GET') ? ((strpos($url,
                            '?') === false) ? '?' : '') . $data : ''), false, $context);
        }
        // show output or error
        if ($output !== false) {
            // show header
            /** @var array $http_response_header */
            if (!empty ($http_response_header)) {
                $ignoreHeaders = array(
                    'HTTP/',
                    'Server: ',
                    'Date: ',
                    'Last-Modified: ',
                    'Connection: '
                );
                foreach ($http_response_header as $header) {
                    $ignore = false;
                    foreach ($ignoreHeaders as $ignoreHeader) {
                        if (substr($header, 0, strlen($ignoreHeader)) === $ignoreHeader) {
                            $ignore = true;
                            break;
                        }
                    }
                    if (!$ignore) {
                        header($header);
                    }
                }
            }
            // show content
            echo $output;
        } else {
            header('HTTP/1.1 500 Internal Server Error');
        }
        // close connection
        wp_die();
    }
}
