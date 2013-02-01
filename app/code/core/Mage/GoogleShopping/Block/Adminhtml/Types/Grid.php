<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Content Item Types Mapping grid
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Types_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('types_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_GoogleShopping_Block_Adminhtml_Types_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_GoogleShopping_Model_Resource_Type_Collection')->addItemsCount();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid colunms
     *
     * @return Mage_GoogleShopping_Block_Adminhtml_Types_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('attribute_set_name',
            array(
                'header'    => $this->__('Attributes Set'),
                'index'     => 'attribute_set_name',
        ));

        $this->addColumn('target_country',
            array(
                'header'    => $this->__('Target Country'),
                'width'     => '150px',
                'index'     => 'target_country',
                'renderer'  => 'Mage_GoogleShopping_Block_Adminhtml_Types_Renderer_Country',
                'filter'    => false
        ));

        $this->addColumn('items_total',
            array(
                'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Total Qty Content Items'),
                'width'     => '150px',
                'index'     => 'items_total',
                'filter'    => false
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId(), '_current'=>true));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
