<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml store grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('storeGrid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Core_Model_Website')
            ->getCollection()
            ->joinGroupAndStore();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('website_title', array(
            'header'        => Mage::helper('Mage_Core_Helper_Data')->__('Website Name'),
            'align'         =>'left',
            'index'         => 'name',
            'filter_index'  => 'main_table.name',
            'renderer'      => 'Mage_Adminhtml_Block_System_Store_Grid_Render_Website'
        ));

        $this->addColumn('group_title', array(
            'header'        => Mage::helper('Mage_Core_Helper_Data')->__('Store Name'),
            'align'         =>'left',
            'index'         => 'group_title',
            'filter_index'  => 'group_table.name',
            'renderer'      => 'Mage_Adminhtml_Block_System_Store_Grid_Render_Group'
        ));

        $this->addColumn('store_title', array(
            'header'        => Mage::helper('Mage_Core_Helper_Data')->__('Store View Name'),
            'align'         =>'left',
            'index'         => 'store_title',
            'filter_index'  => 'store_table.name',
            'renderer'      => 'Mage_Adminhtml_Block_System_Store_Grid_Render_Store'
        ));

        return parent::_prepareColumns();
    }

}
