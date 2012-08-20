<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block filter for Price Permissions Observer
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_PricePermissions_Model_Filter_Block extends Enterprise_PricePermissions_Model_Observer
{
    /**
     * Function name and corresponding block names
     *
     * @var array
     */
    protected $_filterRules = array(
        '_removeStatusMassaction' => array('product.grid', 'admin.product.grid'),
        '_removeColumnPrice' => array('catalog.product.edit.tab.related', 'catalog.product.edit.tab.upsell',
            'catalog.product.edit.tab.crosssell', 'category.product.grid', 'products', 'wishlist', 'compared',
            'rcompared', 'rviewed', 'ordered', 'checkout.accordion.products', 'checkout.accordion.wishlist',
            'checkout.accordion.compared', 'checkout.accordion.rcompared', 'checkout.accordion.rviewed',
            'checkout.accordion.ordered', 'adminhtml.catalog.product.edit.tab.bundle.option.search.grid',
            'admin.product.edit.tab.super.config.grid', 'catalog.product.edit.tab.super.group', 'product.grid',
            'admin.product.grid'),
        '_removeColumnsPriceTotal' => array('admin.customer.view.cart'),
        '_setCanReadPriceFalse' => array('checkout.items', 'items'),
        '_setCanEditReadPriceFalse' => array('catalog.product.edit.tab.downloadable.links',
            'adminhtml.catalog.product.bundle.edit.tab.attributes.price'),
        '_setTabEditReadFalse' => array('product_tabs'),
        '_setOptionsEditReadFalse' => array('admin.product.options'),
        '_setCanEditReadDefaultPrice' => array('adminhtml.catalog.product.bundle.edit.tab.attributes.price'),
        '_setCanEditReadChildBlock' => array('adminhtml.catalog.product.edit.tab.bundle.option'),
        '_hidePriceElements' => array('adminhtml.catalog.product.edit.tab.attributes'),
        '_setFormElementAttributes' => array('catalog.product.edit.tab.super.config.simple')
    );

    /**
     * Call needed function depending on block name
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _filterByBlockName($block)
    {
        $blockName = $block->getNameInLayout();
        foreach ($this->_filterRules as $function => $list) {
            if (in_array($blockName, $list)) {
                call_user_func(array($this, $function), $block);
            }
        }
    }

    /**
     * Remove status option in massaction
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _removeStatusMassaction($block)
    {
        if (!$this->_canEditProductStatus) {
            $block->getMassactionBlock()->removeItem('status');
        }
    }

    /**
     * Remove price column from grid
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _removeColumnPrice($block)
    {
        $this->_removeColumnsFromGrid($block, array('price'));
    }

    /**
     * Remove price and total columns from grid
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _removeColumnsPriceTotal($block)
    {
        $this->_removeColumnsFromGrid($block, array('price', 'total'));
    }

    /**
     * Set read price to false
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setCanReadPriceFalse($block)
    {
        if (!$this->_canReadProductPrice) {
            $block->setCanReadPrice(false);
        }
    }

    /**
     * Set read and edit price to false
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setCanEditReadPriceFalse($block)
    {
        $this->_setCanReadPriceFalse($block);
        if (!$this->_canEditProductPrice) {
            $block->setCanEditPrice(false);
        }
    }

    /**
     * Set edit and read tab to false
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setTabEditReadFalse($block)
    {
        if (!$this->_canEditProductPrice) {
            $block->setTabData('configurable', 'can_edit_price', false);
        }
        if (!$this->_canReadProductPrice) {
            $block->setTabData('configurable', 'can_read_price', false);
        }
    }

    /**
     * Set edit and read price in child block to false
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setOptionsEditReadFalse($block)
    {
        if (!$this->_canEditProductPrice) {
            $optionsBoxBlock = $block->getChildBlock('options_box');
            if (!is_null($optionsBoxBlock)) {
                $optionsBoxBlock->setCanEditPrice(false);
                if (!$this->_canReadProductPrice) {
                    $optionsBoxBlock->setCanReadPrice(false);
                }
            }
        }
    }

    /**
     * Set default product price
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setCanEditReadDefaultPrice($block)
    {
        // Handle Price tab of bundle product
        if (!$this->_canEditProductPrice) {
            $block->setDefaultProductPrice($this->_defaultProductPriceString);
        }
    }

    /**
     * Set edit and read price to child block
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setCanEditReadChildBlock($block)
    {
        // Handle selection prices of bundle product with fixed price
        $selectionTemplateBlock = $block->getChildBlock('selection_template');
        if (!$this->_canReadProductPrice) {
            $block->setCanReadPrice(false);
            if (!is_null($selectionTemplateBlock)) {
                $selectionTemplateBlock->setCanReadPrice(false);
            }
        }
        if (!$this->_canEditProductPrice) {
            $block->setCanEditPrice(false);
            if (!is_null($selectionTemplateBlock)) {
                $selectionTemplateBlock->setCanEditPrice(false);
            }
        }
    }

    /**
     * Hide proce elements
     *
     * @param Mage_Core_Block_Abstract $block
     */
    protected function _hidePriceElements($block)
    {
        // Hide price elements if needed
        $this->_hidePriceElements($block);
    }

    /**
     * Set form element value and readonly
     *
     * @param Mage_Adminhtml_Block_Template $block
     */
    protected function _setFormElementAttributes($block)
    {
        // Handle quick creation of simple product in configurable product
        /** @var $form Varien_Data_Form */
        $form = $block->getForm();
        if (!is_null($form)) {
            if (!$this->_canEditProductStatus) {
                $statusElement = $form->getElement('simple_product_status');
                if (!is_null($statusElement)) {
                    $statusElement->setValue(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
                    $statusElement->setReadonly(true, true);
                }
            }
        }
    }

    /**
     * Handle adminhtml_block_html_before event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function adminhtmlBlockHtmlBefore($observer)
    {
        /** @var $block Mage_Adminhtml_Block_Template */
        $block = $observer->getBlock();

        $this->_filterByBlockName($block);

        // Handle prices that are shown when admin reviews customers shopping cart
        if (stripos($block->getNameInLayout(), 'customer_cart_') === 0) {
            if (!$this->_canReadProductPrice) {
                if ($block->getParentBlock()->getNameInLayout() == 'admin.customer.carts') {
                    $this->_removeColumnFromGrid($block, 'price');
                    $this->_removeColumnFromGrid($block, 'total');
                }
            }
        }
    }

    /**
     * Remove columns from grid
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $block
     * @param array $columns
     */
    protected function _removeColumnsFromGrid($block, array $columns)
    {
        if (!$this->_canReadProductPrice) {
            foreach ($columns as $column) {
                $this->_removeColumnFromGrid($block, $column);
            }
        }
    }

    /**
     * Remove column from grid
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $block
     * @param string $columnId
     * @return Mage_Adminhtml_Block_Widget_Grid|bool
     */
    protected function _removeColumnFromGrid($block, $column)
    {
        if (!$block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            return false;
        }
        return $block->removeColumn($column);
    }
}
