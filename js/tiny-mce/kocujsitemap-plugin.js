/**
 * @file TinyMCE plugin
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 */

(function () {
})(); // empty function for correct minify with comments
//'use strict'; // for jshint uncomment this and comment line above

/* jshint strict: true */
/* jshint -W034 */

/* global ed */
/* global tinymce */

/**
 * TinyMCE plugin
 *
 * @namespace kocujSitemapPluginTinyMce
 * @public
 */
var kocujSitemapPluginTinyMce = {
    /**
     * Initialize TinyMCE plugin
     *
     * @public
     * @param {Object} ed Editor object
     * @param {string} url URL
     * @return {void}
     */
    init: function (ed, url) {
        'use strict';
        // add button
        ed.addButton('kocujsitemap', {
            title: ed.getLang('kocujsitemap.buttontitle'),
            cmd: 'kocujsitemap',
            image: url + '/addsitemap2.png'
        });
        // add command
        ed.addCommand('kocujsitemap', function () {
            var shortcode = '[KocujSitemap]';
            ed.execCommand('mceInsertContent', 0, shortcode);
        });
    },

    /**
     * Create control
     *
     * @public
     * @param {string} n Control type
     * @param {Object} cm Control object
     * @return {Object} Control object
     */
    createControl: function (n, cm) {
        'use strict';
        // exit
        return null;
    },

    /**
     * Get TinyMCE plugin information
     *
     * @public
     * @return {Object} TinyMCE plugin information
     */
    getInfo: function () {
        'use strict';
        // exit
        return {
            longname: ed.getLang('kocujsitemap.longname'),
            author: 'Dominik Kocuj',
            authorurl: 'http://kocuj.pl',
            infourl: 'http://kocujsitemap.wpplugin.kocuj.pl',
            version: '2.1.0'
        };
    }
};

(function () {
    'use strict';
    tinymce.create('tinymce.plugins.KocujSitemap', kocujSitemapPluginTinyMce);
    tinymce.PluginManager.add('kocujsitemap', tinymce.plugins.KocujSitemap);
})();
