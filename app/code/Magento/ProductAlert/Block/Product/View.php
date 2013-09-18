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
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * Current product instance
     *
     * @var null|\Magento\Catalog\Model\Product
     */
    protected $_product = null;

    /**
     * Helper instance
     *
     * @var \Magento\ProductAlert\Helper\Data
     */
    protected $_helper;

    /**
     * @param Magento_Core_Block_Template_Context $context
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\ProductAlert\Helper\Data $productAlertData
     * @param Magento_ProductAlert_Helper_Data $helper
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_ProductAlert_Helper_Data $helper,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_registry = $registry;
        $this->_helper = $helper;
    }

    /**
     * Get current product instance
     *
     * @return \Magento\ProductAlert\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $product = $this->_registry->registry('current_product');
        if ($product && $product->getId()) {
            $this->_product = $product;
        }

        return parent::_prepareLayout();
    }
}
