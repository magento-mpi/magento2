<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance grid block
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('widgetInstanceGrid');
        $this->setDefaultSort('instance_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Widget_Model_Resource_Widget_Instance_Collection */
        $collection = Mage::getModel('Mage_Widget_Model_Widget_Instance')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('instance_id', array(
            'header'    => Mage::helper('Mage_Widget_Helper_Data')->__('Widget ID'),
            'align'     => 'left',
            'index'     => 'instance_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('Mage_Widget_Helper_Data')->__('Widget Instance Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('Mage_Widget_Helper_Data')->__('Type'),
            'align'     => 'left',
            'index'     => 'instance_type',
            'type'      => 'options',
            'options'   => $this->getTypesOptionsArray()
        ));

        $this->addColumn('package_theme', array(
            'header'    => Mage::helper('Mage_Widget_Helper_Data')->__('Design Package/Theme'),
            'align'     => 'left',
            'index'     => 'package_theme',
            'type'      => 'theme',
            'with_empty' => true,
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('Mage_Widget_Helper_Data')->__('Sort Order'),
            'width'     => '100',
            'align'     => 'center',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        $widgets = array();
        $widgetsOptionsArr = Mage::getModel('Mage_Widget_Model_Widget_Instance')->getWidgetsOptionArray();
        foreach ($widgetsOptionsArr as $widget) {
            $widgets[$widget['value']] = $widget['label'];
        }
        return $widgets;
    }

    /**
     * Retrieve design package/theme options array
     *
     * @return array
     */
    public function getPackageThemeOptionsArray()
    {
        $packageThemeArray = array();
        $packageThemeOptions = Mage::getModel('Mage_Core_Model_Design_Source_Design')->getAllOptions(false);
        foreach ($packageThemeOptions as $item) {
            if (is_array($item['value'])) {
                foreach ($item['value'] as $valueItem) {
                    $packageThemeArray[$valueItem['value']] = $valueItem['label'];
                }
            } else {
                $packageThemeArray[$item['value']] = $item['label'];
            }
        }
        return $packageThemeArray;
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('instance_id' => $row->getId()));
    }
}
