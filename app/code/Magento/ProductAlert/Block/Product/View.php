<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product view price and stock alerts
 */
class Magento_ProductAlert_Block_Product_View extends Magento_Core_Block_Template
{
    /**
     * Current product instance
     *
     * @var null|Magento_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Helper instance
     *
     * @var null|Magento_ProductAlert_Helper_Data
     */
    protected $_helper = null;

    /**
     * Product alert data
     *
     * @var Magento_ProductAlert_Helper_Data
     */
    protected $_productAlertData = null;

    /**
     * @param Magento_ProductAlert_Helper_Data $productAlertData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_ProductAlert_Helper_Data $productAlertData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_productAlertData = $productAlertData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check whether the stock alert data can be shown and prepare related data
     *
     * @return void
     */
    public function prepareStockAlertData()
    {
        if (!$this->_getHelper()->isStockAlertAllowed() || !$this->_product || $this->_product->isAvailable()) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_getHelper()->getSaveUrl('stock'));
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     *
     * @return void
     */
    public function preparePriceAlertData()
    {
        if (!$this->_getHelper()->isPriceAlertAllowed()
            || !$this->_product || false === $this->_product->getCanShowPrice()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_getHelper()->getSaveUrl('price'));
    }

    /**
     * Get current product instance
     *
     * @return Magento_ProductAlert_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $product = Mage::registry('current_product');
        if ($product && $product->getId()) {
            $this->_product = $product;
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve helper instance
     *
     * @return Magento_ProductAlert_Helper_Data|null
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = $this->_productAlertData;
        }
        return $this->_helper;
    }
}
