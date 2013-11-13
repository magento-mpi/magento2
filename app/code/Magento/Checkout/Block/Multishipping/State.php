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
 * Multishipping checkout state
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class State extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Checkout\Model\Type\Multishipping\State
     */
    protected $_multishippingState;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Checkout\Model\Type\Multishipping\State $multishippingState
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Checkout\Model\Type\Multishipping\State $multishippingState,
        array $data = array()
    ) {
        $this->_multishippingState = $multishippingState;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        return $this->_multishippingState->getSteps();
    }
}
