=== Kocuj Sitemap ===

Contributors: domko
Tags: sitemap, menus, pages, posts, authors, tags, taxonomies, custom posts types, widget, html 5, shortcode, multilingual
Author URI: http://kocuj.pl
Plugin URI: http://kocujsitemap.wpplugin.kocuj.pl
Requires at least: 5.4
Tested up to: 5.5
Requires PHP: 5.3
Stable tag: 2.7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a sitemap by shortcode, widget or PHP function. Multilingualism (qTranslate X plugin) is supported. WP and WP Multisite.

== Description ==

*Kocuj Sitemap* plugin adds shortcode `[KocujSitemap]` that puts the sitemap in the place where it is located. This allows you to display links to all of your posts, pages, menu items, authors, tags and custom types entries anywhere on your website - even within the article. There is also a PHP function that allows you to place the sitemap anywhere on the website. There is also possibility to use the widget to place a sitemap anywhere in the sidebar.

This plugin supports multilingual websites. If you have installed the plugin compatible with *Kocuj Sitemap* plugin (currently it is *qTranslate X*), the plugin will generate a sitemap on your website in accordance with the currently selected language. If you do not have the plugin that supports multilingualism, *Kocuj Sitemap* plugin will display a sitemap for the default language defined for your WordPress installation.

The sitemap is automatically generated and stored in the cache, which is used in the sitemap to avoid using the database when loading your website, after each change of any element in the administration panel (for example, when you change a post). This process speeds up the loading of the sitemap on your website.

The *Kocuj Sitemap* plugin can be used in standard or in multisite WordPress installation.

= Features =

* Compatibility with HTML 5,
* Multilingual websites support (with qTranslate X plugin),
* Fast operation due to use of the cache,
* Displaying posts, pages, menu items, authors, tags and custom types entries (with custom taxonomies),
* Displaying all elements with dividing it by sections or without it,
* Ability to change order of displayed entries,
* Ability to exclude selected entries,
* Ability to hide selected element types, for example, all posts,
* Easily adding of the sitemap by using a shortcode with help of HTML or visual editor button,
* Possibility to set left margin for each level in multi-level sitemap if theme has problems with displaying such a list,
* Many configuration options,
* Many filters for better control of the plugin.

= Requirements =

This plugin requires PHP 5.x (from 5.3) or PHP 7.x and WordPress 4.9 or greater. It works in standard and multisite WordPress environments. It is strongly recommended to use WordPress in the newest version. This plugin uses shortcodes so in Gutenberg (a WordPress editor from version 5.0) it will not work as block and shortcode should be added manually.

= How to use =

There are few possibilities to display the sitemap:
* by using a shortcode `[KocujSitemap]` inside a post of any type,
* by displaying a widget,
* by using the PHP function anywhere in the code, for example, inside a theme.

The shortcode `[KocujSitemap]` has optional parameters:

* `homelinktext` - text that will be used as the text for the link to the main page,
* `class` - name of the style sheet class that will be added to the block element (`div` or `nav`) containing the entire sitemap,
* `excludepost` - comma separated list of identifiers for posts of any type (posts, pages, custom types entries) to exclude,
* `excludecategory` - comma separated list of identifiers for post categories to exclude,
* `excludeauthor` - comma separated list of identifiers for authors to exclude,
* `excludeterm` - comma separated list of identifiers for post categories, post tags or custom taxonomies to exclude,
* `hidetypes` - comma separated list of element types to hide; it can contains the following types: "author" (to hide authors), "custom" (to hide custom post types), "home" (to hide link to homepage), "menu" (to hide menus), "page" (to hide pages), "post" (to hide posts), "tag" (to hide tags).

For example, if you use `[KocujSitemap homelinktext="NEW LINK TEXT" class="new_class"]`, the sitemap will be displayed in the block element (`div` or `nav`) with the CSS class `new_class` and link to the home page with text `NEW LINK TEXT`.

If you add other parameters, for example `[KocujSitemap homelinktext="NEW LINK TEXT" class="new_class" excludepost="5,6" excludeterm="12" hidetypes="page"]`, the sitemap will be displayed in the block element (`div` or `nav`) with the CSS class `new_class`, link to the home page with text `NEW LINK TEXT`, there will be not displayed posts of any type with identifiers 5 and 6 and post tags and custom taxonomies with identifier 12 and all pages will be not displayed.

Instead of using the shortcode, you can edit the PHP file responsible for the theme. *Kocuj Sitemap* plugin defines global PHP function which declaration is as follows: `<?php function kocujsitemap_show_sitemap($homeLinkText = '', $class = '', array $exclude = array(), array $hideTypes = array()); ?>`. The parameters `$homeLinkText` and `$class` perform the same function as the corresponding parameters in the shortcode `[KocujSitemap]`.

For example, if you use `<?php kocujsitemap_show_sitemap('NEW LINK TEXT', 'new_class'); ?>`, the sitemap will be displayed in the block element (`div` or `nav`) with the CSS class `new_class` and link to the home page with text `NEW LINK TEXT`.

More things should be sayed about `$exclude` parameter. It should contains an array of identifiers categorized (with correct key identifier) by entry type to exclude. There are the following entries types available:

* `post` - for excluding posts of any type (posts, pages, custom types entries),
* `category` - for excluding post categories,
* `author` - for excluding authors,
* `term` - for excluding post categories, post tags or custom taxonomies.

For example, the `$exclude` array containing `array('post' => array(5, 6), 'term' => array(12))` will exclude posts of any type with identifiers 5 and 6 and post tags and custom taxonomies with identifier 12.

The parameter $hideTypes has list of elements types to hide. It can contains the following types: "author" (to hide authors), "custom" (to hide custom post types), "home" (to hide link to homepage), "menu" (to hide menus), "page" (to hide pages), "post" (to hide posts), "tag" (to hide tags).

For example, the `$hideTypes` array containing `array('post', 'page')` will hide all posts and pages.

There is also possibility to use widget. It has possibility to display the sitemap as standard or drop-down list. It has also the possibility to exclude selected elements or hide the selected elements types from the sitemap.

= Configuration =

There is option *Sitemap* in the administration panel, which is used to configure the behavior of the *Kocuj Sitemap* plugin. If you select any submenu item from the *Sitemap* menu, you will find yourself in a place where you can set specific options for the plugin.

The settings on each administration page are divided into tabs. There can be active only one tab at once. Tab is selected by clicking on its name.

Each tab contains a set of options. Each option has a description that is displayed when you set mouse cursor over it. You can also see more detailed information by clicking a *Help* button on top of the page.

Changes in the configuration can be saved by clicking on the *Save settings* button. There is a possibility to restore the settings that were set after installing the plugin, by selecting an option *Restore settings* in the plugin menu.

Remember, that removing the plugin will clean all its options saved into the database.

= CSS classes =

The *Kocuj Sitemap* plugin displays a sitemap using a few CSS classes, so you can customize the look of the sitemap to your requirements.

There are the following CSS classes available:

* `kocujsitemap` - used by main container for the sitemap (`nav` or `div`) in shortcode, PHP function and widget,
* `kocujsitemapwidget` - used by main container for the sitemap (`nav` or `div`) in the widget,
* `kocujsitemap-home` - used by `<li>` element in the sitemap when the link is to the home page,
* `kocujsitemap-post` - used by `<li>` element in the sitemap when the link is to the post of any type (post, page, custom type entry),
* `kocujsitemap-category` - used by `<li>` element in the sitemap when the link is to the post category,
* `kocujsitemap-term` - used by `<li>` element in the sitemap when the link is to the post tags or custom taxonomy,
* `kocujsitemap-author` - used by `<li>` element in the sitemap when the link is to the author,
* `kocujsitemap-unknown` - used by `<li>` element in the sitemap when the link is to something different than all above (for example, link to other website).

= Planned features =

* Generating one sitemap for all blogs in multisite installation,
* Creating more than one sitemap,
* Easy control of shortcode parameters in HTML and visual editor,
* Generating XML sitemap for search engines.

= Contact =

If you have any suggestion, feel free to use the contact form at [http://kocujsitemap.wpplugin.kocuj.pl/contact/](http://kocujsitemap.wpplugin.kocuj.pl/contact/).

If you want to have a regular information about this plugin, please become a fan of plugin on Facebook: [http://www.facebook.com/kocujsitemap](http://www.facebook.com/kocujsitemap)

See also official plugin website: [http://kocujsitemap.wpplugin.kocuj.pl](http://kocujsitemap.wpplugin.kocuj.pl)

== For developers ==

The *Kocuj Sitemap* plugin contains a set of filters that allow you to change some behavior of the plugin. This allows you to adapt the plugin to the requirements of the developer of another plugin or theme without making changes to the code in *Kocuj Sitemap* plugin.

*Kocuj Sitemap* plugin contains the following filters:

***kocujsitemap_additional_multiple_languages_php_classes***

*Parameters:*

1. array - a list of additional PHP classes that support multilingual plugins and they are not built-in into the *Kocuj Sitemap* plugin

*Returned value:*

1. array - a list of PHP classes that support multilingual plugins and they are not built-in into the *Kocuj Sitemap* plugin

*Description:*

This filter adds a PHP class for supporting multilingual websites. To add a new PHP class, you need to add a new element to array which contains the following fields:

* `filename` - full path to the file with a new PHP class,
* `class` - PHP class name.

More information about this functionality can be found by looking into file *classes/multiple-languages/template-for-developers/some-plugin.class.php* which can be a good start point for developer of another PHP class. Remember, that this PHP class should implements the interface *\KocujSitemapPlugin\Interfaces\Language*.

***kocujsitemap_default_custom***

*Parameters:*

1. array - a list of custom post types slugs

*Returned value:*

1. array - a list of custom post types slugs

*Description:*

This filter sets the default list of custom post types that appear in the sitemap if the list in the "custom post types posts list options" in administration panel is empty.

***kocujsitemap_default_exclude_author***

*Parameters:*

1. string - comma separated list with authors identifiers to exclude

*Returned value:*

1. string - comma separated list with authors identifiers to exclude

*Description:*

This filter sets the default authors list to exclude in shortcode `[KocujSitemap]`.

***kocujsitemap_default_exclude_category***

*Parameters:*

1. string - comma separated list with post categories identifiers to exclude

*Returned value:*

1. string - comma separated list with post categories identifiers to exclude

*Description:*

This filter sets the default post categories list to exclude in shortcode `[KocujSitemap]`.

***kocujsitemap_default_exclude_post***

*Parameters:*

1. string - comma separated list with posts, pages and custom types posts identifiers to exclude

*Returned value:*

1. string - comma separated list with posts, pages and custom types posts identifiers to exclude

*Description:*

This filter sets the default posts list to exclude in shortcode `[KocujSitemap]`.

***kocujsitemap_default_exclude_term***

*Parameters:*

1. string - comma separated list with post tags and custom taxonomies identifiers to exclude

*Returned value:*

1. string - comma separated list with post tags and custom taxonomies identifiers to exclude

*Description:*

This filter sets the default post tags and custom taxonomies list to exclude in shortcode `[KocujSitemap]`.

***kocujsitemap_default_home_link_text***

*Parameters:*

1. string - text with default text for link to the main page
2. string - current locale

*Returned value:*

1. string - text with default text for link to the main page

*Description:*

This filter sets the default text that is used in the link to the main page in the sitemap. It is used if there is not specified `homelinktext` parameter in the `[KocujSitemap]` shortcode or if there is not specified `$homeLinkText` parameter in the `kocujsitemap_show_sitemap` PHP function.

***kocujsitemap_default_main_css_class***

*Parameters:*

1. string - text with all default CSS classes for a sitemap container (block element `div` or `nav`)

*Returned value:*

1. string - text with all default CSS classes for a sitemap container (block element `div` or `nav`)

*Description:*

This filter sets the default CSS class that is used in the block element of the sitemap. It is used if there is not specified `class` parameter in the `[KocujSitemap]` shortcode or if there is not specified `$class` parameter in the `kocujsitemap_show_sitemap` PHP function.

***kocujsitemap_default_menus***

*Parameters:*

1. array - a list of menus identifiers

*Returned value:*

1. array - a list of menus identifiers

*Description:*

This filter sets the default list of menus that appear in the sitemap if the list in the "menus list options" in administration panel is empty.

***kocujsitemap_element***

*Parameters:*

1. string - text for link to current element (the entire HTML tag `<a>`)
2. int - element identifier or 0 for link to the main page
3. string - element type; available values: "post" for post, "page" for page, "menu" for menu item, "category" for post category, "author" for author, "tag" for post tag, "custom" for custom post type entry, "taxonomy" for custom taxonomy and "home" for link to the main page
4. string - current locale

*Returned value:*

1. string - text for link to current element (the entire HTML tag `<a>`)

*Description:*

This filter sets the text for link to current element.

***kocujsitemap_element_home_link_text_position***

*Parameters:*

1. int - position of text in link to main page
2. string - text for link to current element (the entire HTML tag `<a>`)
3. string - current locale

*Returned value:*

1. int - position of text in link to main page

*Description:*

This filter sets the position of text in link to main page. For example, if link is `<a href="mainpage.html">Main page</a>`, the position of text in link to the main page will be set to `14`.

***kocujsitemap_first_element_css_class***

*Parameters:*

1. string - text with CSS class for first element in the sitemap

*Returned value:*

1. string - text with CSS class for first element in the sitemap

*Description:*

This filter sets the CSS class that is used in the first element of the sitemap.

***kocujsitemap_link_text***

*Parameters:*

1. string - text with link text
2. int - element identifier or 0 for link to the main page
3. string - element type; available values: "post" for post, "page" for page, "menu" for menu item, "category" for post category, "author" for author, "tag" for post tag, "custom" for custom post type entry, "taxonomy" for custom taxonomy and "home" for link to the main page
4. string - current locale

*Returned value:*

1. string - text with link text

*Description:*

This filter sets the link text.

***kocujsitemap_widget_default_exclude_author***

*Parameters:*

1. string - comma separated list with authors identifiers to exclude

*Returned value:*

1. string - comma separated list with authors identifiers to exclude

*Description:*

This filter sets the default authors list to exclude in the widget.

***kocujsitemap_widget_default_exclude_category***

*Parameters:*

1. string - comma separated list with post categories identifiers to exclude

*Returned value:*

1. string - comma separated list with post categories identifiers to exclude

*Description:*

This filter sets the default post categories list to exclude in the widget.

***kocujsitemap_widget_default_exclude_post***

*Parameters:*

1. string - comma separated list with posts, pages and custom types posts identifiers to exclude

*Returned value:*

1. string - comma separated list with posts, pages and custom types posts identifiers to exclude

*Description:*

This filter sets the default posts list to exclude in the widget.

***kocujsitemap_widget_default_exclude_term***

*Parameters:*

1. string - comma separated list with post tags and custom taxonomies identifiers to exclude

*Returned value:*

1. string - comma separated list with post tags and custom taxonomies identifiers to exclude

*Description:*

This filter sets the default post tags and custom taxonomies list to exclude in the widget.

***shortcode_atts_kocujsitemap***

*Parameters:*

1. array - output array of shortcode attributes
2. array - accepted parameters and their defaults
3. array - input array of shortcode attributes

*Returned value:*

1. array - output array of shortcode attributes

*Description:*

This filter changes attributes for the `[KocujSitemap]` shortcode.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload all files to the `/wp-content/plugins/kocuj-sitemap` directory,
2. Activate the plugin through the *Plugins* menu in WordPress,
3. Configure in *Sitemap* option in administration panel,
4. Make one of the following action: use a shortcode `[KocujSitemap]` inside a post of any type, display a widget or use the PHP function anywhere in the code, for example, inside a theme.

== Screenshots ==

1. An example of the sitemap.
2. An example of the sitemap divided into sections.
3. This plugin has options to choose what should be displayed in the sitemap.
4. User can easily select elements order in the sitemap.
5. User can easily select menus which should be displayed in the sitemap.
6. User can easily select custom post types which should be displayed in the sitemap.
7. This plugin is compatible with qTranslate X plugin.
8. There is possibility to set custom name for each section in all supported languages.
9. For each settings page there is help information.

== Frequently Asked Questions ==

Some questions below are from users of this plugin and some are from author of this plugin as supplement to the documentation.

= How can I add question about this plugin? =

Please send a question to author at [http://kocujsitemap.wpplugin.kocuj.pl/contact/](http://kocujsitemap.wpplugin.kocuj.pl/contact/).

= Why this plugin is compatible only with last two versions of WordPress? =

There is too often the situation when old version of WordPress is used and no one performs updates. No update of WordPress makes it difficult for plugin developers (such as *Kocuj Sitemap*) to do technical support and backward compatibility. Therefore, I have decided that only 2 versions of WordPress will be supported - the newest and previous. For example, when the latest WordPress version is 4.3, then versions 4.2 and 4.3 will be supported.

But what should do people with old versions of WordPress? The answer is simple - to update.

Please be aware that an outdated version of WordPress is nothing more but trouble. Sooner or later your website will get hacked. Therefore, WordPress update process should be performed as often as possible.

But why people do not update their WordPress installations? There are often the same few reasons. I will try to argue with them below.

* *Plugin, which we are using, will stop working:* If a plugin author abandoned it and no longer provides updates, you should look for another plugin that provides the same or similar functionality. You possibly can, in order to repair this, hire a specialist who will repair the plugin, find another plugin or write his own solution. Sometimes it turns out that plugin, that is not marked by its author as working in the latest version of WordPress, works fine. So you should test it.
* *Theme or the entire site will be inoperable:* If your theme is not working correctly in the latest version of WordPress, please contact with its author and determine the conditions for correcting these bugs. Alternatively, as in the case of plugin, you may employ a specialist, who will repair a theme.
* *My changes in WordPress core will be lost:* Never, ever, do not change anything in WordPress core! At first update of WordPress core, all your changes will be lost! If you need some change in WordPress, you should find plugin with the appropriate functionality.

If, despite these arguments, you still want to have the old version of WordPress, then you will not can use latest versions of *Kocuj Sitemap* plugin. Of course, you can try, but author of this plugin can't guarantee that all of its functionality will be working correctly.

= How to create more than one sitemap? =

It is not possible to create more than one sitemap on one website. The only possibility to do this is to have a multisite WordPress installation and add the sitemap to each site in network individually.

The idea for this plugin was to create one global sitemap with links to all neccessary places. However, because some people need multiple sitemaps, this feature is planned for one of the future version of this plugin. More information will be available when it will be added to plugin development schedule.

= I can see wrong links titles and some entries are not displayed in the sitemap. What should I do? =

Sometimes some plugins can change some content used in the sitemap, for example, post title. It is very rare situation, however it is possible.

There is possibility to update the content and also to make sure that it will not happen again. You can go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*) and click on the *Save settings* button. During the saving of settings there is a process of recreating the cache, so the new content will be used from this moment.

To make sure that it will not happen again you can disable the cache. It is strongly not recommended, because it can slow down the loading of the website and it will use more resources to get data from database. However, if you see that one of your plugins is changing the content, you should disable the cache to prevent displaying a wrong data in your sitemap. To do so you should go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*), click on the tab *Troubleshooting*, uncheck the option *Enable cache* and click on the *Save settings* button. From now on the *Kocuj Sitemap* plugin will not be used cache.

= My multi-level sitemap has no indentations. What should I do? =

Sometimes the multi-level sitemap is displayed without additional left margins. It is not this plugin fault, but it is problem in your theme! This plugin does not create the sitemap appearance. It is task for your theme. When this problem occurs, it is not relate only to the sitemap, but to all multi-level lists on your website. You should contact with your theme author and he should fix this bug.

If you can't fix this problem for some reason (author of theme is unavailable, he can't fix the bug, etc.), the *Kocuj Sitemap* plugin has the solution. However, this solution is only for the sitemap - problems with all other multi-level lists will not be corrected in any way. To fix the problem with multi-level sitemap, you should go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*), click on the tab *Troubleshooting*, enter the required left margin in field *Force left margin in pixels for each level on multi-level list* and click on the *Save settings* button.

= How can I remove duplicates from the sitemap? =

In case of menus, there is possibility to have duplicates, because different menus can have some entries the same as others. If you want to remove this duplicates from the sitemap, you should go to the menus settings for the *Kocuj Sitemap* plugin (submenu *Menus list options*), click on the tab *Options*, check the option *Remove menu items duplicates from sitemap* and click on the *Save settings* button.

= The sitemap looks strange. What should I do? =

The look of the sitemap is not a functionality of the *Kocuj Sitemap* plugin, but the theme which is used on your website. The sitemap use correct HTML tags, so with any theme it will be look like it should be.

There can be one possibility for problems with the sitemap look. You should check if your theme is created in HTML 5. If not, you have to make sure that sitemap is also not generated as HTML 5. To do so you should go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*), click on the tab *Sitemap format*, uncheck the option *Use HTML 5 tags* and click on the *Save menus list options* button.

= I have a multi-lingual plugin, but my sitemap is only in one language - the default for my WordPress installation. Why? =

First of all you should check if your multi-lingual plugin is supported by the *Kocuj Sitemap* plugin. For now there is one multi-lingual plugin which is used: qTranslate X. Remember, that any fork of these plugins can also not working, because there can be some changes in classes or functions names. The *Kocuj Sitemap* plugin guarantees full cooperation only with this one plugin at this time - any other plugin can be ignored or worked only partially.

However, if you are using one of the supported plugins and you have some problems, you should check if you have no more than one multi-lingual plugin installed and activated. It is always a bad idea to use more than one multi-lingual plugin at once. However, if you have any reason to use more than one multi-lingual plugin at once, you should do some checks in the *Kocuj Sitemap* plugin settings. To do so go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*), click on the tab *Plugins for multilingualism* and check which multi-lingual plugin is displayed as *Currently used plugin*. If you want to change it, you should select the required multi-lingual plugin in the *Use plugin for multiple languages* option and click on the *Save settings* button.

There is also possibility to have some other plugin which interferes with your content. There is possibility to update the content and also to make sure that it will not happen again. You can go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*) and click on the *Save settings* button. During the saving of settings there is a process of recreating the cache, so the new content will be used from this moment.

To make sure that it will not happen again you can disable the cache. It is strongly not recommended, because it can slow down the loading of the website and it will use more resources to get data from database. However, if you see that one of your plugins is changing the content, you should disable the cache to prevent displaying a wrong data in your sitemap. To do so you should go to the administration settings for the *Kocuj Sitemap* plugin (submenu *Basic settings*), click on the tab *Troubleshooting*, uncheck the option *Enable cache* and click on the *Save settings* button. From now on the *Kocuj Sitemap* plugin will not be used cache.

== Changelog ==

= 2.7.0 =
* 0000383: [Other] No declaration of used namespaces.
* 0000384: [Other] Reformat code to PSR-2.
* 0000385: [Bug] Some security problems.

= 2.6.4 =
* 0000382: [Bug] Some errors during saving options.

= 2.6.3 =
* 0000381: [Bug] There is an exception about non-existing window, when WordPress is displayed in generated windows, such as, for adding media.

= 2.6.2 =
* 0000379: [Bug] Custom posts types are not available in administration panel if they are created by some plugin (for ex. CPT UI).

= 2.6.1 =
Added missing JavaScript files.

= 2.6.0 =
* 0000378: [Feature] Add widget options for exclude entire types of elements from displaying.
* 0000375: [Feature] Add shortcode attributes for excluding entire posts types.
* 0000366: [Feature] Add option to display sitemap as drop-down menu in widget.
* 0000371: [Feature] Remove checking if array is empty before "foreach".
* 0000365: [Bug] Disallow entering float value when integer is required in plugin settings.

= 2.5.1 =
* 0000364: [Bug] Remove unneeded actions and filters from "customizer", because it can make "customizer" working incorrectly in some cases.

= 2.5.0 =
* 0000350: [Feature] Change some texts for options and documentation.
* 0000363: [Feature] Do not allow to send information about website to the plugin's author, when URL is local.
* 0000362: [Bug] Cache is not removed for site which has been deleted from Multisite network.
* 0000358: [Feature] Add "support" and "help in translation" buttons to settings pages.
* 0000353: [Feature] Add option for removing duplicates when displaying different menus.
* 0000361: [Bug] Disabling taxonomies for custom posts types is not working.
* 0000359: [Feature] Add option for adding "powered by" text in the sitemap widget.
* 0000349: [Feature] Reorganize settings in administration panel.
* 0000352: [Feature] Optimize for memory usage.
* 0000356: [Bug] Modal window in administration panel is not displayed correctly in mobile devices.
* 0000355: [Bug] Meta boxes in settings are not displayed correctly in mobile devices.
* 0000341: [Feature] Remove old methods (without any and with MultipleLanguages interface) of adding custom support for multi-lingual plugins.
* 0000351: [Feature] Add possibility to disable multi-lingualism for the plugin.
* 0000345: [Feature] Allow set if uninstallation of the plugin will remove its settings from database or not.
* 0000348: [Bug] When in multisite, old versions data are only updating for the main site.
* 0000286: [Feature] Reorganize code which supports plugin settings.
* 0000347: [Feature] Change one-time widget to multiple widgets.
* 0000282: [Feature] Remove usage of "jQuery UI" for tabs in administration panel.
* 0000344: [Feature] Remove unneeded checking of settings values in JavaScript files.
* 0000340: [Feature] Remove old filters from versions 1.x.x.
* 0000339: [Bug] Use of PHP constants in context when it can be wrong.

= 2.4.0 =
* 0000354: [Feature] Add support for WordPress 4.7.

= 2.3.5 =
* 0000343: [Bug] Sometimes removing the plugin from administration panel is not possible.

= 2.3.4 =
* 0000342: [Bug] PHP fatal error for updating in PHP 5.3.

= 2.3.3 =
* 0000338: [Bug] Error on frontend on websites without multiple languages.

= 2.3.2 =
* 0000337: [Bug] Update from older versions can failed.
* 0000336: [Bug] Attributes added without space between tag and attribute in HTML 5.

= 2.3.1 =
* 0000335: [Bug] Administration panel is locked with error.

= 2.3.0 =
* 0000329: [Feature] Optimize size of plugin.
* 0000334: [Bug] Wrong plugin review link.
* 0000333: [Feature] Do not force displaying update information, but show link to it in top message.
* 0000332: [Feature] Add old methods of custom support for multi-language to deprecated list.
* 0000331: [Feature] Remove "tag*" CSS classes from HTML elements.
* 0000318: [Feature] Remove support for qTranslate plugin.
* 0000330: [Bug] Custom PHP classes for multi-lingual support are not working.
* 0000327: [Bug] Incorrect setting permissions when creating a directory.
* 0000328: [Bug] Change directories separator "/" to constant DIRECTORY_SEPARATOR for correctly working in Windows system.

= 2.2.5 =
* 0000325: [Bug] When sitemap is generated in frontend, some texts are not translated.
* 0000326: [Bug] Plugin qTranslate is detected when only qTranslate X is installed and activated when compatibility mode is set.

= 2.2.4 =
* 0000320: [Bug] Links to license, link to sending thanks to author and update info in administration panel are not working in multisite mode.

= 2.2.3 =
* 0000319: [Bug] There is an error in PHP when using options for custom post types.

= 2.2.2 =
* 0000317: [Bug] Sometimes categories or other terms are not translated into another language.

= 2.2.1 =
* 0000316: [Bug] Update do not save required data to database.

= 2.2.0 =
* 0000315: [Feature] Optimize database queries in administration panel.
* 0000314: [Feature] Reorganize and optimize some code.
* 0000312: [Feature] Add information about removing support for qTranslate.
* 0000313: [Bug] Sometimes post categories are not sorted correctly.

= 2.1.0 =
* 0000301: [Feature] Make plugin PHP 7 compatible.
* 0000311: [Bug] It is impossible to exclude something in sitemap widget.
* 0000303: [Feature] Add better errors handling.
* 0000310: [Bug] Shortcode button in visual editor is missing.
* 0000309: [Bug] Cache directory is not always automatically created.
* 0000290: [Feature] Optimize JS scripts.
* 0000299: [Feature] Add option for setting left margin in the sitemap.
* 0000307: [Feature] Use PHP 7 function "random_int" instead of "rand" when it is required to have better randomize of value.
* 0000304: [Bug] Correct escaping HTML attributes.
* 0000302: [Feature] Add JsDoc documentation to all JavaScript files.
* 0000300: [Feature] Minimize JavaScript scripts in administration panel.
* 0000288: [Feature] Remove unneeded namespaces fragments.
* 0000287: [Feature] Reorganize code for Quicktags and TinyMCE buttons.
* 0000285: [Feature] Do not load unneeded JS scripts in administration panel.
* 0000308: [Feature] Remove unneeded "eval" in loading cache security file.

= 2.0.6 =
* 0000289: [Bug] Some other plugins cannot be saved in administration panel when "Kocuj Sitemap" plugin is activated.

= 2.0.5 =
* 0000284: [Bug] Add support for all WordPress types for qTranslate support.

= 2.0.4 =
* 0000283: [Bug] Fix support for qTranslate plugin.

= 2.0.3 =
* 0000281: [Bug] Wrong display of multiple menus.

= 2.0.2 =
Correct versioning for WordPress repository (2.0.1 cannot be updated).

= 2.0.1 =
* 0000280: [Bug] Wrong display of the selected menus in administration panel.

= 2.0.0 =
* 0000279: [Bug] Fix sending "thanks" when administration panel is using "https" protocol.
* 0000154: [Feature] Add possibility for disable cache; sometimes it is necessary for cooperation with other plugins.
* 0000204: [Bug] Fix problems with posts in hierarchical categories.
* 0000206: [Feature] Remove compatibility with WordPress versions older than 4.3.
* 0000270: [Bug] Fix wrong sorting by names and titles when multi-lingual plugins are in use.
* 0000145: [Feature] Add more help information.
* 0000276: [Feature] Add deprecated debug information for plugin actions and filters that are not used anymore.
* 0000277: [Feature] Change filters names to better compatibility with original WordPress filters names.
* 0000271: [Feature] Optimize cache file size.
* 0000266: [Feature] Add using qTranslate-X for multi-lingual content.
* 0000267: [Feature] Set translations to be compatible with requirements for translate.wordpress.org.
* 0000223: [Feature] Change all comparision to strict comparisions in PHP and JavaScript.
* 0000264: [Feature] Block sending "thanks" when IP is local.
* 0000207: [Bug] When "qTranslate" plugin is installed or configured after "Kocuj Sitemap" plugin, cache can have untranslated texts.
* 0000216: [Feature] Move cache directory to "wp-content/cache/kocuj-sitemap" directory.
* 0000215: [Feature] Remove all extract() functions from code.
* 0000213: [Feature] Remove PHP end tags.
* 0000205: [Feature] Change HTML 5 compatible mode for IE to conditional comments, instead of checking of browser in PHP.
* 0000169: [Feature] Add compatibility with multiple blogs.
* 0000202: [Feature] Add more filters.
* 0000178: [Feature] Add window in administration panel after update from version 1.x.x with information about important changes in version 2.0.0.
* 0000179: [Feature] Add possibility for change section name for each language.
* 0000181: [Feature] Remove unnecessary "eval()" from code.
* 0000180: [Feature] Remove cron job for plugin.
* 0000168: [Feature] Add more options for each type displayed in the sitemap.
* 0000142: [Feature] Add widget with sitemap.
* 0000160: [Feature] Add possibility to exclude other entries than posts or pages, for example, categories.
* 0000150: [Bug] Changes in permalinks settings should regenerate cache.
* 0000162: [Feature] Secure cache directory.
* 0000157: [Feature] Reorganize plugin menu in administration panel.
* 0000153: [Feature] Reorganize entire core.
* 0000158: [Feature] Add information about possibility of review of this plugin.
* 0000148: [Feature] Add support for custom posts types.
* 0000159: [Feature] Add possibility to display authors list and tags list.
* 0000149: [Feature] Add possibility for dividing a sitemap into sections (posts, pages, etc.).
* 0000146: [Feature] Allow administrator to disable sitemap button from HTML and visual editor.
* 0000156: [Feature] Generate cache in frontend (for compatibility with some plugin, especially excluding posts) and add option to disable it.
* 0000143: [Feature] Add options to exclude some posts and pages from the sitemap.
* 0000155: [Feature] Add option "settings" to plugins menu in administration panel.

= 1.3.3 =
* 0000167: [Bug] Correct creating a sitemap cache too many times after changing any option.

= 1.3.2 =
There are no new features or bug fixes - only information if your server or hosting account is prepared for version 2.0.0 of this plugin.

= 1.3.1 =
* 0000268: [Bug] Fix incompatibility with standard menu WordPress class.
* 0000269: [Feature] Add "index.html" to all directories for more security.

= 1.3.0 =
* 0000161: [Feature] Add French language.

= 1.2.0 =
* 0000139: [Bug] Fix plugin registering hook.
* 0000140: [Bug] Fix security in forms in administration panel.
* 0000127: [Feature] Add support for qTranslate plugin to correctly display links and titles.
* 0000129: [Feature] Add additional author information to administration panel (like Facebook link).
* 0000130: [Feature] Add button in visual and HTML editor for automatically adding a plugin shortcode.
* 0000131: [Feature] Add more text for users to help and readme.
* 0000132: [Feature] Add additional help information to each section in administration menu for this plugin.
* 0000133: [Feature] Add possibility to change menu list in the plugin settings without reloading the entire page.
* 0000134: [Feature] Change possibility of sending thanks to author to be possible after 1 day of using this plugin (not just after installing it).
* 0000135: [Feature] Change loading PHP classes for administration panel to do it only in backend to speed up scripts execution.
* 0000136: [Feature] Add possibility to change order of menus list in administration settings for this plugin.

= 1.1.1 =
* 0000137: [Bug] Fix comptability with PHP 5.4 series.

= 1.1.0 =
* 0000123: [Bug] Check and correct any problems with readme.txt for correct displaying information on wordpress.org repository.
* 0000122: [Feature] Add plugin help to administration panel.

= 1.0.0 =
* First version of plugin.

== Upgrade Notice ==

= 2.6.4 =
Fixed bug in administration panel.

= 2.6.3 =
Fixed bug for window for adding media.

= 2.6.2 =
Fixed bug for custom post types.

= 2.6.1 =
Added missing JavaScript files.

= 2.6.0 =
Added possibility to exclude entire elements types (for example, all posts), added possibility to display sitemap as drop-down lists in widget.

= 2.5.1 =
Fixed rare bug in "customizer".

= 2.5.0 =
Added removing duplicates. Some optimization. Fixed some bugs.

= 2.4.0 =
Added support for WordPress 4.7.

= 2.3.5 =
Fixed one bug.

= 2.3.4 =
Fixed one bug.

= 2.3.3 =
Fixed one bug.

= 2.3.2 =
Fixed some bugs.

= 2.3.1 =
Fixed critical bug.

= 2.3.0 =
Removed support for qTranslate plugin, optimized some code and fixed few bugs.

= 2.2.5 =
Fixed one bug.

= 2.2.4 =
Fixed one bug in administration panel for multisite.

= 2.2.3 =
Fixed one bug.

= 2.2.2 =
Fixed one bug.

= 2.2.1 =
Fixed one bug.

= 2.2.0 =
Optimized and reorganized some code. Fixed some bugs.

= 2.1.0 =
Added compatibility with PHP 7 and possibility to force left margins in multi-level sitemap. Fixed some bugs.

= 2.0.6 =
Fixed bug with saving settings for other plugins.

= 2.0.5 =
Fixed bug with unavailable support for all WordPress types in qTranslate.

= 2.0.4 =
Fixed bug with qTranslate support.

= 2.0.3 =
Fixed bug with displaying multiple menus.

= 2.0.2 =
No new features or bug fixes - only correction of versioning in WordPress repository.

= 2.0.1 =
Fixed bug in administration panel.

= 2.0.0 =
WARNING: THIS VERSION REQUIRES MINIMUM 4.3 VERSION OF WORDPRESS AND MINIMUM 5.3 VERSION OF PHP! CHANGED FILTERS! Added: authors, tags and custom posts types entries; excluding elements; dividing sitemap into sections; qTranslate X support; contextual help; more control. Fixed minor bugs.

= 1.3.3 =
Fix bug with creating a sitemap cache too many times after changing any option.

= 1.3.2 =
No new features or bug fixes - only information if your server or hosting account is prepared for version 2.0.0 of this plugin.

= 1.3.1 =
Fixed few minor bugs.

= 1.3.0 =
Added French languages. Changed some texts about plugin.

= 1.2.0 =
Added support for multilingual websites using the qTranslate plugin. Changed option "menu list" in administration panel to allow changing values and their order without forcing to reloading page. Fixes some minor bugs.

= 1.1.1 =
Fixed problems with compatibility with PHP 5.4 series.

= 1.1.0 =
Added help to administration panel.
