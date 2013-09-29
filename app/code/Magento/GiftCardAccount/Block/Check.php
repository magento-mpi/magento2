<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Check result block for a Giftcardaccount
 */
namespace Magento\GiftCardAccount\Block;

class Check extends \Magento\Core\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get current card instance from registry
     *
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function getCard()
    {
        return $this->_coreRegistry->registry('current_giftcardaccount');
    }

    /**
     * Check whether a gift card account code is provided in request
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getRequest()->getParam('giftcard-code', '');
    }
}
