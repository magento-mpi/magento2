<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard most ordered products grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Dashboard\Tab\Products;

class Ordered extends \Magento\Adminhtml\Block\Dashboard\Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsOrderedGrid');
    }

    protected function _prepareCollection()
    {
        if (!\Mage::helper('Magento\Core\Helper\Data')->isModuleEnabled('Magento_Sales')) {
            return $this;
        }
        if ($this->getParam('website')) {
            $storeIds = \Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getParam('group')) {
            $storeIds = \Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
        }

        $collection = \Mage::getResourceModel('\Magento\Sales\Model\Resource\Report\Bestsellers\Collection')
            ->setModel('\Magento\Catalog\Model\Product')
            ->addStoreFilter($storeId)
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'sortable'  => false,
            'index'     => 'product_name'
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'width'     => '120px',
            'type'      => 'currency',
            'currency_code' => (string) \Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'sortable'  => false,
            'index'     => 'product_price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => __('Order Quantity'),
            'width'     => '120px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'qty_ordered',
            'type'      => 'number'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /*
     * Returns row url to show in admin dashboard
     * $row is bestseller row wrapped in Product model
     *
     * @param \Magento\Catalog\Model\Product $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        // getId() would return id of bestseller row, and product id we get by getProductId()
        $productId = $row->getProductId();

        // No url is possible for non-existing products
        if (!$productId) {
            return '';
        }

        $params = array('id' => $productId);
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        return $this->getUrl('*/catalog_product/edit', $params);
    }
}
