<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance grid block
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsWidgetInstanceGrid');
        $this->setDefaultSort('instance_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /* @var $collection Mage_Cms_Model_Mysql4_Page_Collection */
        $collection = Mage::getModel('enterprise_cms/widget_instance')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('instance_id', array(
            'header'    => Mage::helper('enterprise_cms')->__('Widget ID'),
            'align'     => 'left',
            'index'     => 'instance_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('enterprise_cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('enterprise_cms')->__('Type'),
            'align'     => 'left',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => $this->getTypesOptionsArray()
        ));

        $this->addColumn('package_theme', array(
            'header'    => Mage::helper('enterprise_cms')->__('Design Package/Theme'),
            'align'     => 'left',
            'index'     => 'package_theme',
            'type'      => 'options',
            'options'   => $this->getPackageThemeOptionsArray()
        ));

        $this->addColumn('layout_handler', array(
            'header'    => Mage::helper('enterprise_cms')->__('Layout Handler'),
            'align'     => 'left',
            'index'     => 'layout_handler',
        ));

        $this->addColumn('layout_reference', array(
            'header'    => Mage::helper('enterprise_cms')->__('Layout Reference'),
            'align'     => 'left',
            'index'     => 'layout_reference',
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
        $widgetsXml = Mage::getModel('cms/widget')->getXmlConfig();
        foreach ($widgetsXml->getNode('widgets')->children() as $item) {
            if ($type = $item->getAttribute('type')) {
                $widgets[$type] = (string)Mage::helper('enterprise_cms')->__('%s', $item->name);
            }
        }
        return $widgets;
    }

    public function getPackageThemeOptionsArray()
    {
        $packageThemeArray = array();
        foreach (Mage::getModel('core/design_source_design')->getAllOptions(false, true) as $item) {
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