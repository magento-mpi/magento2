<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product view other block
 */
namespace Magento\Review\Block\Product\View;

class Other extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }
}
