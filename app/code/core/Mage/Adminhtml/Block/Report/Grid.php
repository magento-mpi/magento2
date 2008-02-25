<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_storeSwitcherVisibility = true;

    protected $_dateFilterVisibility = true;

    protected $_exportVisibility = true;

    protected $_filters = array();

    protected $_defaultFilters = array(
            'report_from' => '',
            'report_to' => '',
            'report_period' => 'day'
        );

    protected $_reportModel;

    protected $_subReportSize;

    public function __construct()
    {

        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setTemplate('report/grid.phtml');
        $this->setUseAjax(false);
    }

    protected function _prepareLayout()
    {
        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
                ->setTemplate('report/store/switcher.phtml')
        );

        $this->setChild('refresh_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Refresh'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'task'
                ))
        );
        parent::_prepareLayout();
        return $this;
    }

    protected function _prepareColumns()
    {
        foreach ($this->_columns as $_column) {
            $_column->setSortable(false);
        }

        parent::_prepareColumns();
    }

    /**
     * Set visibility of store switcher
     *
     * @param boolean $visible
     */
    public function setStoreSwitcherVisibility($visible=true)
    {
        $this->_storeSwitcherVisibility = $visible;
    }

    /**
     * Return visibility of store switcher
     *
     * @return boolean
     */
    public function getStoreSwitcherVisibility()
    {
        return $this->_storeSwitcherVisibility;
    }

    /**
     * Return store switcher html
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Set visibility of date filter
     *
     * @param boolean $visible
     */
    public function setDateFilterVisibility($visible=true)
    {
        $this->_dateFilterVisibility = $visible;
    }

    /**
     * Return visibility of date filter
     *
     * @return boolean
     */
    public function getDateFilterVisibility()
    {
        return $this->_dateFilterVisibility;
    }

    /**
     * Set visibility of export action
     *
     * @param boolean $visible
     */
    public function setExportVisibility($visible=true)
    {
        $this->_exportVisibility = $visible;
    }

    /**
     * Return visibility of export action
     *
     * @return boolean
     */
    public function getExportVisibility()
    {
        return $this->_exportVisibility;
    }

    protected function _initReportModel()
    {
        if (!$this->_reportModel) {
            $this->_reportModel = Mage::getModel('reports/report');
        }
    }

    public function getReportModel()
    {
        if (!$this->_reportModel) {
            $this->_initReportModel();
        }
        return $this->_reportModel;
    }

    public function getPeriods()
    {
        return array(
            'day'=>$this->__('1 Day'),
            'month'=>$this->__('1 Month'),
		    'year'=>$this->__('1 Year')
        );
        //return $this->getReportModel()->getPeriods();
    }

    public function getDateFormat()
    {
        return $this->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * Return refresh button html
     */
    public function getRefreshButtonHtml()
    {
        return $this->getChildHtml('refresh_button');
    }

    protected function _setFilterValues($data)
    {
        foreach ($data as $name => $value) {
            //if (isset($data[$name])) {
                $this->setFilter($name, $data[$name]);
            //}
        }
        return $this;
    }

    public function setFilter($name, $value)
    {
        if ($name) {
            $this->_filters[$name] = $value;
        }
    }

    public function getFilter($name)
    {
        if (isset($this->_filters[$name])) {
            return $this->_filters[$name];
        } else {
            return '';
        }
    }

    public function setSubReportSize($size)
    {
        $this->_subReportSize = $size;
    }

    public function getSubReportSize()
    {
        return 5;
    }

    protected function _prepareCollection()
    {
        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        $collection = Mage::getResourceModel('reports/report_collection');

        $collection->setPeriod($this->getFilter('report_period'));
        $collection->setInterval(
            $this->getLocale()->date($this->getFilter('report_from'), Zend_Date::DATE_SHORT),
            $this->getLocale()->date($this->getFilter('report_to'), Zend_Date::DATE_SHORT)
            );

        $this->setCollection($collection);
    }

    /**
     * Retrieve locale
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

    /**
     * Add new export type to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  Mage_Adminhtml_Block_Widget_Grid
     */
    public function addExportType($url, $label)
    {
        $this->_exportTypes[] = new Varien_Object(
            array(
                'url'   => $this->getUrl($url, array('_current'=>true, 'filter'=>null)),
                'label' => $label
            )
        );
        return $this;
    }
}
