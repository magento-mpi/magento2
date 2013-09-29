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
     * @var \Magento\Core\Model\Registry
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
     * @param \Magento\Core\Block\Template\Context $context
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\ProductAlert\Helper\Data $productAlertData
     * @param \Magento\ProductAlert\Helper\Data $helper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\ProductAlert\Helper\Data $helper,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\Data $coreData,
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
