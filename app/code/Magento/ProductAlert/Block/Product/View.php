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
     * @var \Magento\ProductAlert\Helper\Data
     */
    protected $_productAlertData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\ProductAlert\Helper\Data $productAlertData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\ProductAlert\Helper\Data $productAlertData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
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
