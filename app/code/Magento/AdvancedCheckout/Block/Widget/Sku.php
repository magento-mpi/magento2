<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order by SKU Widget Block
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
namespace Magento\AdvancedCheckout\Block\Widget;

class Sku
    extends \Magento\AdvancedCheckout\Block\Sku\AbstractSku
    implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Math\Random $mathRandom,
        array $data = array()
    ) {
        $this->mathRandom = $mathRandom;
        parent::__construct($checkoutData, $coreData, $context, $mathRandom, $data);
    }
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('checkout/cart/advancedAdd');
    }
}
