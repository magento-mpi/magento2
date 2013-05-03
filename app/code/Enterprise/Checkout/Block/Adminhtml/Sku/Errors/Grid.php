<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Add-by-SKU grid with errors
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid ID
     *
     * @param array $attributes
     */
    protected function _construct($attributes = array())
    {
        parent::_construct($attributes);
        $this->setId('sku_errors');
        $this->setRowClickCallback(null);
    }

    /**
     * Prepare collection of errors
     *
     * @return Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        $removeButtonHtml = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button', '', array(
            'data' => array(
                'class' => 'delete',
                'label' => 'Remove',
                'onclick' => 'addBySku.removeFailedItem(this)',
                'type' => 'button',
            )
        ))->toHtml();
        /* @var $parentBlock Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract */
        $parentBlock = $this->getParentBlock();
        foreach ($parentBlock->getFailedItems() as $affectedItem) {
            // Escape user-submitted input
            if (isset($affectedItem['item']['qty'])) {
                $affectedItem['item']['qty'] = empty($affectedItem['item']['qty'])
                    ? ''
                    : (float)$affectedItem['item']['qty'];
            }
            $item = new Varien_Object();
            $item->setCode($affectedItem['code']);
            if (isset($affectedItem['error'])) {
                $item->setError($affectedItem['error']);
            }
            $item->addData($affectedItem['item']);
            $item->setId($item->getSku());
            /* @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('Mage_Catalog_Model_Product');
            if (isset($affectedItem['item']['id'])) {
                $productId = $affectedItem['item']['id'];
                $item->setProductId($productId);
                $product->load($productId);
                /* @var $stockStatus Mage_CatalogInventory_Model_Stock_Status */
                $stockStatus = Mage::getModel('Mage_CatalogInventory_Model_Stock_Status');
                $status = $stockStatus->getProductStatus($productId, $this->getWebsiteId());
                if (!empty($status[$productId])) {
                    $product->setIsSalable($status[$productId]);
                }
                $item->setPrice(Mage::helper('Mage_Core_Helper_Data')->formatPrice($product->getPrice()));
            }
            $descriptionBlock = $this->getLayout()->createBlock(
                'Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description',
                '',
                array('data' => array('product' => $product, 'item' => $item))
            );
            $item->setDescription($descriptionBlock->toHtml());
            $item->setRemoveButton($removeButtonHtml);
            $collection->addItem($item);
        }
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Describe columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('description', array(
            'header'   => $this->__('Product'),
            'index'    => 'description',
            'class'    => 'no-link',
            'sortable' => false,
            'renderer' => 'Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Renderer_Html',
        ));

        $this->addColumn('price', array(
            'header'   => $this->__('Price'),
            'class'    => 'no-link',
            'width'    => 100,
            'index'    => 'price',
            'sortable' => false,
            'type'     => 'text',
        ));

        $this->addColumn('qty', array(
            'header'   => $this->__('Quantity'),
            'class'    => 'no-link sku-error-qty',
            'width'    => 40,
            'sortable' => false,
            'index'    => 'qty',
            'renderer' => 'Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Renderer_Qty',
        ));

        $this->addColumn('remove', array(
            'header'   => $this->__('Remove'),
            'class'    => 'no-link',
            'width'    => 80,
            'index'    => 'remove_button',
            'sortable' => false,
            'renderer' => 'Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Renderer_Html',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Disable unnecessary functionality
     *
     * @return Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid
     */
    public function _prepareLayout()
    {
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        return $this;
    }

    /**
     * Retrieve row css class for specified item
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getRowClass(Varien_Object $item)
    {
        if ($item->getCode() == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            return 'qty-not-available';
        }
        return '';
    }

    /**
     * Get current website ID
     *
     * @return int|null|string
     */
    public function getWebsiteId()
    {
        return $this->getParentBlock()->getStore()->getWebsiteId();
    }

    /**
     * Retrieve empty row urls for the grid
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }
}
