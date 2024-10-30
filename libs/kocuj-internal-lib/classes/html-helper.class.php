<?php

/**
 * html-helper.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

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
final class HtmlHelper
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
     * Get HTML tag attributes
     *
     * @access private
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; class of HTML element), "data" (array type; values for "data-*" tags), "id" (string type; id of HTML element), "style" (string type; style of HTML element), "styleclassfilter" (array type; it contains two fields: "projectobj" with object of class \KocujIL\V13a\Classes\Project for current project and "filter" with filter name which will be added to project prefix)
     * @return string HTML tag attributes
     */
    private function getHtmlAttrs(array $attr)
    {
        // parse style and class
        if (isset ($attr ['styleclassfilter'])) {
            $styleClassString = $attr ['styleclassfilter'] ['projectobj']->get('project-helper')->applyFiltersForHTMLStyleAndClass($attr ['styleclassfilter'] ['filter'],
                '', array(
                    'defaultclass' => (isset ($attr ['class'])) ? $attr ['class'] : '',
                    'defaultstyle' => (isset ($attr ['style'])) ? $attr ['style'] : ''
                ));
        } else {
            $styleClassString = ((isset ($attr ['style'])) ? ' style="' . esc_attr($attr ['style']) . '"' : '') . ((isset ($attr ['class'])) ? ' class="' . esc_attr($attr ['class']) . '"' : '');
        }
        // parse data attributes
        $dataString = '';
        if (isset ($attr ['data'])) {
            foreach ($attr ['data'] as $dataId => $dataVal) {
                $dataString .= ' data-' . $dataId . '="' . esc_attr($dataVal) . '"';
            }
        }
        // exit
        return ((isset ($attr ['id'])) ? ' id="' . esc_attr($attr ['id']) . '"' : '') . $dataString . $styleClassString;
    }

    /**
     * Get beginning of link anchor
     *
     * @access public
     * @param string $link
     *            Link URL
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; class of HTML element), "data" (array type; values for "data-*" tags), "external" (bool type; if true, link is external and will be opened in new tab), "externalwithouttarget" (bool type; if true, link is external, but there will be no "target" attribute; if there is also an "external" attribute in $attr array, the "external" will be used instead), "id" (string type; id of HTML element), "noescapeurl" (bool type; if true, link is not escaped), "rel" (string type; "rel" link attribute value), "style" (string type; style of HTML element), "styleclassfilter" (array type; it contains two fields: "projectobj" with object of class \KocujIL\V13a\Classes\Project for current project and "filter" with filter name which will be added to project prefix), "title" (string type; title of the link) - default: empty
     * @return string Beginning of link anchor
     */
    public function getLinkBegin($link, array $attr = array())
    {
        // prepare "rel" atribute
        $relValue = (isset ($attr ['rel'])) ? ' ' . $attr ['rel'] : '';
        $relAttr = ((isset ($attr ['rel'])) && (isset ($attr ['rel'] [0]) /* strlen($attr['rel']) > 0 */) && ((!isset ($attr ['external'])) || ((isset ($attr ['external'])) && (!$attr ['external'])))) ? ' rel="' . esc_attr($attr ['rel']) . '"' : '';
        // exit
        return '<a href="' . (((!isset ($attr ['noescapeurl'])) || ((isset ($attr ['noescapeurl'])) && (!$attr ['noescapeurl']))) ? esc_url($link) : $link) . '"' . $relAttr . ((((isset ($attr ['external'])) && ($attr ['external'])) || ((isset ($attr ['externalwithouttarget'])) && ($attr ['externalwithouttarget']))) ? ' rel="' . esc_attr('external' . $relValue) . '"' . (((!isset ($attr ['externalwithouttarget'])) || (!$attr ['externalwithouttarget'])) ? ' target="_blank"' : '') : '') . (((isset ($attr ['title'])) && (isset ($attr ['title'] [0]) /* strlen($attr['title']) > 0 */)) ? ' title="' . esc_attr($attr ['title']) . '"' : '') . $this->getHtmlAttrs($attr) . '>';
    }

    /**
     * Get ending of link anchor
     *
     * @access public
     * @return string Ending of link anchor
     */
    public function getLinkEnd()
    {
        // exit
        return '</a>';
    }

    /**
     * Get link anchor
     *
     * @access public
     * @param string $link
     *            Link URL
     * @param string $name
     *            Link name; if empty, link URL will be used instead - default: empty
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; class of HTML element), "data" (array type; values for "data-*" tags), "external" (bool type; if true, link is external and will be opened in new tab), "externalwithouttarget" (bool type; if true, link is external, but there will be no "target" attribute; if there is also an "external" attribute in $attr array, the "external" will be used instead), "id" (string type; id of HTML element), "noescapeurl" (bool type; if true, link is not escaped), "style" (string type; style of HTML element), "styleclassfilter" (array type; it contains two fields: "projectobj" with object of class \KocujIL\V13a\Classes\Project for current project and "filter" with filter name which will be added to project prefix), "title" (string type; title of the link) - default: empty
     * @return string Link anchor
     */
    public function getLink($link, $name = '', array $attr = array())
    {
        // exit
        return $this->getLinkBegin($link,
                $attr) . ((isset ($name [0]) /* strlen($name) > 0 */) ? $name : ((substr($link, 0,
                    2) === '//') ? $this->getUrlProtocol() . ':' . $link : $link)) . $this->getLinkEnd();
    }

    /**
     * Get e-mail link anchor
     *
     * @access public
     * @param string $mail
     *            E-mail address
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; class of HTML element), "data" (array type; values for "data-*" tags), "id" (string type; id of HTML element), "style" (string type; style of HTML element), "styleclassfilter" (array type; it contains two fields: "projectobj" with object of class \KocujIL\V13a\Classes\Project for current project and "filter" with filter name which will be added to project prefix) - default: empty
     * @return string E-mail link anchor
     */
    public function getMailLink($mail, array $attr = array())
    {
        // exit
        return '<a href="' . esc_url('mailto:' . $mail) . '"' . $this->getHtmlAttrs($attr) . '>' . $mail . '</a>';
    }

    /**
     * Get HTML image
     *
     * @access public
     * @param string $url
     *            Image URL
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "class" (string type; class of HTML element), "data" (array type; values for "data-*" tags), "id" (string type; id of HTML element), "noescapeurl" (bool type; if true, link is not escaped), "style" (string type; style of HTML element), "styleclassfilter" (array type; it contains two fields: "projectobj" with object of class \KocujIL\V13a\Classes\Project for current project and "filter" with filter name which will be added to project prefix), "title" (string type; title of the image) - default: empty
     * @return string HTML image
     */
    public function getImage($url, array $attr = array())
    {
        // add default style
        $style = 'border-width:0;border-style:none;';
        if (isset ($attr ['style'])) {
            $attr ['style'] .= $style;
        } else {
            $attr ['style'] = $style;
        }
        // exit
        return '<img src="' . (((!isset ($attr ['noescapeurl'])) || ((isset ($attr ['noescapeurl'])) && (!$attr ['noescapeurl']))) ? esc_url($url) : $url) . '" alt=""' . (((isset ($attr ['title'])) && (isset ($attr ['title'] [0]) /* strlen($attr['title']) > 0 */)) ? ' title="' . esc_attr($attr ['title']) . '"' : '') . $this->getHtmlAttrs($attr) . '/>';
    }
}
