<?php

/**
 * list-table.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsForm;

// set namespaces aliases
use KocujIL\V13a\Enums\Project\Components\All\Options\Order;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\BulkAvailable;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\NonceRequired;
use KocujIL\V13a\Enums\ProjectCategory;
use WP_List_Table;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings form list table class
 *
 * @access public
 */
class ListTable extends WP_List_Table
{

    /**
     * Component "settings-form" object
     *
     * @access private
     * @var object
     */
    private $componentObj;

    /**
     * Container identifier
     *
     * @access private
     * @var string
     */
    private $containerId;

    /**
     * List of columns displayed on data set list
     *
     * @access private
     * @var array
     */
    private $containerColumns;

    /**
     * Columns callbacks for displaying on data set list
     *
     * @access private
     * @var array
     */
    private $columnsCallbacks;

    /**
     * First column identifier
     *
     * @access private
     * @var string
     */
    private $firstColumnId = '';

    /**
     * List of sortable columns
     *
     * @access private
     * @var array
     */
    private $containerSortableColumns = array();

    /**
     * Actions links
     *
     * @access private
     * @var array
     */
    private $actionsLinks = array();

    /**
     * Bulk actions list
     *
     * @access private
     * @var array
     */
    private $bulkActions = array();

    /**
     * Constructor
     *
     * @access public
     * @param object $componentObj
     *            Object for "settings-form" component
     * @param string $containerId
     *            Container identifier
     * @param array $containerColumns
     *            List of columns displayed on data set list - default: empty
     * @param array $containerSortableColumns
     *            List of sortable columns - default: empty
     * @param string $orderColumn
     *            Column for ordering - default: empty
     * @param array $columnsCallbacks
     *            Columns callbacks - default: empty
     * @param array $controllersList
     *            Data set controllers - default: empty
     * @param array $availableControllers
     *            List of controllers from $controllersList which will be used - default: empty
     * @return void
     */
    public function __construct(
        $componentObj,
        $containerId,
        array $containerColumns = array(),
        array $containerSortableColumns = array(),
        $orderColumn = '',
        array $columnsCallbacks = array(),
        array $controllersList = array(),
        array $availableControllers = array()
    ) {
        // remember settings
        $this->componentObj = $componentObj;
        $this->containerId = $containerId;
        $this->containerColumns = $containerColumns;
        $this->columnsCallbacks = $columnsCallbacks;
        if (!empty ($containerColumns)) {
            $values = array_keys($containerColumns);
            $this->firstColumnId = $values [0];
        }
        foreach ($containerSortableColumns as $column) {
            $this->containerSortableColumns [$column] = array(
                $column,
                ($column === $orderColumn)
            );
        }
        // prepare action links
        if (!empty ($controllersList)) {
            $isNonce = false;
            $noncePar = '';
            foreach ($controllersList as $key => $val) {
                if (in_array($key, $availableControllers)) {
                    if ($val ['bulkavailable'] === BulkAvailable::YES) {
                        $this->bulkActions [$key] = $val ['title'];
                    }
                    $this->containerColumns = array_merge(array(
                        'cb' => '<input type="checkbox" />'
                    ), $this->containerColumns);
                    if ((!$isNonce) && ($val ['noncerequired'] === NonceRequired::YES)) {
                        $noncePar = '_wpnonce=' . wp_create_nonce(Component::getDataSetNonceId($componentObj,
                                $this->containerId)) . '&';
                        $isNonce = true;
                    }
                    $this->actionsLinks [$key] = sprintf('<a href="' . esc_url('?page=%s&action=%s&' . Component::getFieldIdDataSet($componentObj) . '=%s&id=%s&' . $noncePar . 'return_to=%s') . '">%s</a>',
                        sanitize_text_field($_REQUEST ['page']), $key, $this->containerId, '%s',
                        (isset ($_SERVER ['REQUEST_URI'])) ? urlencode($_SERVER ['REQUEST_URI']) : '', $val ['title']);
                }
            }
        }
        // execute parent
        parent::__construct(array(
            'plural' => $this->containerId
        ));
    }

    /**
     * Get columns list
     *
     * @access public
     * @return array Columns list
     */
    public function get_columns()
    {
        // exit
        return $this->containerColumns;
    }

    /**
     * Get sortable columns list
     *
     * @access public
     * @return array Sortable columns list
     */
    public function get_sortable_columns()
    {
        // exit
        return $this->containerSortableColumns;
    }

    /**
     * Get bulk actions list
     *
     * @access public
     * @return array Bulk actions list
     */
    public function get_bulk_actions()
    {
        // exit
        return $this->bulkActions;
    }

    /**
     * Prepare items
     *
     * @access public
     * @return void
     */
    public function prepare_items()
    {
        // initialize
        $perPage = $this->get_items_per_page($this->componentObj->getProjectObj()->getMainSettingInternalName() . '__' . $this->containerId . '__per_page',
            10);
        // set columns header
        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns()
        );
        // set pagination
        $this->set_pagination_args(array(
            'total_items' => $this->componentObj->getComponent('options',
                ProjectCategory::ALL)->getDataSetElementsCount($this->containerId),
            'per_page' => $perPage
        ));
        // get data
        $order = Order::ASC;
        if ((isset ($_GET ['order'])) && (sanitize_text_field($_GET ['order']) === 'desc')) {
            $order = Order::DESC;
        }
        $this->items = $this->componentObj->getComponent('options',
            ProjectCategory::ALL)->getDataSetElements($this->containerId, array(),
            (isset ($_GET ['orderby'])) ? sanitize_text_field($_GET ['orderby']) : '', $order, $perPage,
            ($this->get_pagenum() - 1) * $perPage);
    }

    /**
     * Get column data to display
     *
     * @access public
     * @param array $item
     *            Item data
     * @param string $columnName
     *            Column name
     * @return string Column data to display
     */
    public function column_default($item, $columnName)
    {
        // execute column display callback
        if (isset ($this->columnsCallbacks [$columnName])) {
            $item [$columnName] = call_user_func_array($this->columnsCallbacks [$columnName], array(
                $columnName,
                $item ['ID'],
                $item [$columnName]
            ));
        }
        // prepare actions links
        if ($columnName === $this->firstColumnId) {
            $actionsLinks = array();
            foreach ($this->actionsLinks as $key => $val) {
                $actionsLinks [$key] = str_replace('%s', $item ['ID'], $val);
            }
            $actionLinksString = $this->row_actions($actionsLinks);
        } else {
            $actionLinksString = '';
        }
        // exit
        return sprintf('%1$s %2$s',
            ((isset ($this->containerColumns [$columnName])) && (isset ($item [$columnName]))) ? $item [$columnName] : '-',
            $actionLinksString);
    }

    /**
     * Get column data for checkboxes
     *
     * @access public
     * @return string Column data for checkboxes
     */
    public function column_cb($item)
    {
        // exit
        if (!empty ($this->bulkActions)) {
            return sprintf('<input type="checkbox" name="id[]" value="%s" />', esc_attr($item ['ID']));
        } else {
            return '';
        }
    }
}
