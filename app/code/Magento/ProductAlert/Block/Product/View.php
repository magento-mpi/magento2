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
namespace Magento\ProductAlert\Block\Product;

class View extends \Magento\Core\Block\Template
{
    /**
     * Current product instance
     *
     * @var null|\Magento\Catalog\Model\Product
     */
    protected $_product = null;

    /**
     * Product alert data
     *
     * @var Magento_ProductAlert_Helper_Data
     */
    protected $_productAlertData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_ProductAlert_Helper_Data $productAlertData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_ProductAlert_Helper_Data $productAlertData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_productAlertData = $productAlertData;
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check whether the stock alert data can be shown and prepare related data
     *
     * @return void
     */
    public function prepareStockAlertData()
    {
        if (!$this->_productAlertData->isStockAlertAllowed()
            || !$this->_product || $this->_product->isAvailable()) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_productAlertData->getSaveUrl('stock'));
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     *
     * @return void
     */
    public function preparePriceAlertData()
    {
        if (!$this->_productAlertData->isPriceAlertAllowed()
            || !$this->_product || false === $this->_product->getCanShowPrice()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_productAlertData->getSaveUrl('price'));
    }

    /**
     * Get current product instance
     *
     * @return \Magento\ProductAlert\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $product = $this->_coreRegistry->registry('current_product');
        if ($product && $product->getId()) {
            $this->_product = $product;
        }

        return parent::_prepareLayout();
    }
}
