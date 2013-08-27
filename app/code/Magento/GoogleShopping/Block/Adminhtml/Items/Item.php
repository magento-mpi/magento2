<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Shopping Items
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Items_Item extends Magento_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('items');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items_Item
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_GoogleShopping_Model_Resource_Item_Collection');
        $store = $this->_getStore();
        $collection->addStoreFilter($store->getId());
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items_Item
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name',
            array(
                'header'    => __('Product'),
                'width'     => '30%',
                'index'     => 'name',
        ));

        $this->addColumn('expires',
            array(
                'header'    => __('Expires'),
                'type'      => 'datetime',
                'width'     => '100px',
                'index'     => 'expires',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items_Item
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item');
        $this->setNoFilterMassactionColumn(true);

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => __('Delete'),
             'url'      => $this->getUrl('*/*/massDelete', array('_current'=>true)),
             'confirm'  => __('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('refresh', array(
             'label'    => __('Synchronize'),
             'url'      => $this->getUrl('*/*/refresh', array('_current'=>true)),
             'confirm'  => __('This action will update items\' attributes and remove items that are not available in Google Content. If an attribute was deleted from the mapping, it will also be deleted from Google. Do you want to continue?')
        ));
        return $this;
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

    /**
     * Get store model by request param
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore($this->getRequest()->getParam('store'));
    }
}
