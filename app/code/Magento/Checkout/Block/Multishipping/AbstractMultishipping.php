<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mustishipping checkout base abstract block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class AbstractMultishipping extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Checkout\Model\Type\Multishipping
     */
    protected $_multishipping;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Type\Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Checkout\Model\Type\Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
    }
}
