<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Product\View;

/**
 * Product view other block
 */
class Other extends \Magento\View\Element\Template
{
    /**
     * Core model registry
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }
}
