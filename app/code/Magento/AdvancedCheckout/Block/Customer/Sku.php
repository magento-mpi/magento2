<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Order By SKU block
 *
 */
namespace Magento\AdvancedCheckout\Block\Customer;

class Sku extends \Magento\AdvancedCheckout\Block\Sku\AbstractSku
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Framework\Math\Random $mathRandom,
        array $data = []
    ) {
        parent::__construct($context, $checkoutData, $mathRandom, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('*/*/uploadFile');
    }

    /**
     * Check whether form should be multipart
     *
     * @return bool
     */
    public function getIsMultipart()
    {
        return true;
    }
}
