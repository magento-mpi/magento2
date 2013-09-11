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
 * Adminhtml customer view gift registry items block
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Items
    extends \Magento\Adminhtml\Block\Widget\Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftregistry_customer_items_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\GiftRegistry\Model\Item')->getCollection()
            ->addRegistryFilter($this->getEntity()->getId());

        $collection->updateItemAttributes();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => __('ID'),
            'index'  => 'product_id',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('name', array(
            'header' => __('Product'),
            'index'  => 'product_name'
        ));

        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'index'  => 'sku',
            'width'  => '200px'
        ));

        $this->addColumn('price', array(
            'header' => __('Price'),
            'index'  => 'price',
            'type'  => 'currency',
            'width' => '120px',
            'currency_code' => (string) \Mage::getStoreConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header'   => __('Requested'),
            'index'    => 'qty',
            'width'    => '120px',
            'renderer' => '\Magento\GiftRegistry\Block\Adminhtml\Widget\Grid\Column\Renderer\Qty'
        ));

        $this->addColumn('qty_fulfilled', array(
            'header' => __('Fulfilled'),
            'index'  => 'qty_fulfilled',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('note', array(
            'header' => __('Note'),
            'index'  => 'note',
            'width'  => '120px'
        ));

        $this->addColumn('action', array(
            'header' => __('Action'),
            'width'  => '120px',
            'options'   => array(
                 0 => __('Action'),
                'update' => __('Update Quantity'),
                'remove' => __('Remove Item')
            ),
            'renderer' => '\Magento\GiftRegistry\Block\Adminhtml\Widget\Grid\Column\Renderer\Action'
        ));

        return parent::_prepareColumns();
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
