<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping info block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Product;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory,
        array $data = array()
    ) {
        $this->_wrappingFactory = $wrappingFactory;
        parent::__construct($context, $data);
    }

    /**
     * Return product gift wrapping info
     *
     * @return false|\Magento\Framework\Object
     */
    public function getGiftWrappingInfo()
    {
        $wrappingId = null;
        if ($this->getLayout()->getBlock('additional.product.info')) {
            $wrappingId = $this->getLayout()->getBlock('additional.product.info')->getItem()->getGwId();
        }

        if ($wrappingId) {
            return $this->_wrappingFactory->create()->load($wrappingId);
        }
        return false;
    }
}
