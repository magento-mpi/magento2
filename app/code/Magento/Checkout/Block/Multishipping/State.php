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

class State extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Type\Multishipping\State
     */
    protected $_multishippingState;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Type\Multishipping\State $multishippingState
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Type\Multishipping\State $multishippingState,
        array $data = array()
    ) {
        $this->_multishippingState = $multishippingState;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        return $this->_multishippingState->getSteps();
    }
}
