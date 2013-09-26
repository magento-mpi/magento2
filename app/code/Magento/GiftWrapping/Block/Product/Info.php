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
class Magento_GiftWrapping_Block_Product_Info extends Magento_Core_Block_Template
{
    /**
     * @var Magento_GiftWrapping_Model_WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_GiftWrapping_Model_WrappingFactory $wrappingFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_GiftWrapping_Model_WrappingFactory $wrappingFactory,
        array $data = array()
    ) {
        $this->_wrappingFactory = $wrappingFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return product gift wrapping info
     *
     * @return false|Magento_Object
     */
    public function getGiftWrappingInfo()
    {
        $wrappingId = null;
        if ($this->getLayout()->getBlock('additional.product.info')) {
            $wrappingId = $this->getLayout()->getBlock('additional.product.info')
                ->getItem()
                ->getGwId();
        }

        if ($wrappingId) {
            return $this->_wrappingFactory->create()->load($wrappingId);
        }
        return false;
    }
}
