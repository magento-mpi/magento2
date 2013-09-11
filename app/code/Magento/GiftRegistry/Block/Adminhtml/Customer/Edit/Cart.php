<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer cart items grid block
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Cart
    extends \Magento\Adminhtml\Block\Widget\Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftregistry_customer_cart_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $quote = \Mage::getModel('Magento\Sales\Model\Quote');
        $quote->setWebsite(\Mage::app()->getWebsite($this->getEntity()->getWebsiteId()));
        $quote->loadByCustomer(\Mage::getModel('Magento\Customer\Model\Customer')->load($this->getEntity()->getCustomerId()));

        $collection = ($quote) ? $quote->getItemsCollection(false) : new \Magento\Data\Collection();
        $collection->addFieldToFilter('parent_item_id', array('null' => true));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => __('Product ID'),
            'index'  => 'product_id',
            'type'   => 'number',
            'width'  => '100px',
        ));

        $this->addColumn('name', array(
            'header' => __('Product'),
            'index' => 'name',
        ));

        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'index' => 'sku',
            'width' => '200px',
        ));

        $this->addColumn('price', array(
            'header' => __('Price'),
            'index' => 'price',
            'type'  => 'currency',
            'width' => '120px',
            'currency_code' => (string) \Mage::getStoreConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header' => __('Quantity'),
            'index' => 'qty',
            'type'  => 'number',
            'width' => '120px',
        ));

        $this->addColumn('total', array(
            'header' => __('Total'),
            'index' => 'row_total',
            'type'  => 'currency',
            'width' => '120px',
            'currency_code' => (string) \Mage::getStoreConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action options for this grid
     *
     * @return \Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Cart
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('products');
        $this->getMassactionBlock()->addItem('add', array(
            'label'    => __('Add to Gift Registry'),
            'url'      => $this->getUrl('*/*/add', array('id' => $this->getEntity()->getId())),
            'confirm'  => __('Are you sure you want to add these products?')
        ));

        return $this;
    }

    /**
     * Return grid row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

    /**
     * Return gift registry entity object
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return \Mage::registry('current_giftregistry_entity');
    }
}
