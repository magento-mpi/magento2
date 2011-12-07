<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product view price and stock alerts
 */
class Mage_ProductAlert_Block_Product_View extends Mage_Core_Block_Template
{
    /**
     * Current product instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Check whether the stock alert data can be shown and prepare related data
     */
    public function prepareStockAlertData()
    {
        if (!Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW)
            || !$this->_product || $this->_product->isAvailable()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl(Mage::helper('Mage_ProductAlert_Helper_Data')->getSaveUrl('stock'));
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     */
    public function preparePriceAlertData()
    {
        if (!Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW)
            || !$this->_product || false === $this->_product->getCanShowPrice()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl(Mage::helper('Mage_ProductAlert_Helper_Data')->getSaveUrl('price'));
    }

    /**
     * Get current product instance
     *
     * @return Mage_ProductAlert_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $product = Mage::registry('current_product');
        if ($product && $product->getId()) {
            $this->_product = $product;
        }

        return parent::_prepareLayout();
    }
}
