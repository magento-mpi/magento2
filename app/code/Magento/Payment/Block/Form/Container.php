<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Block\Form;

use Magento\Payment\Model\Method\AbstractMethod;
/**
 * Base container block for payment methods forms
 *
 * @method \Magento\Sales\Model\Quote getQuote()
 *
 * @category   Magento
 * @package    Magento_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Container extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        array $data = array()
    ) {
        $this->_paymentHelper = $paymentHelper;
        parent::__construct($context, $data);
    }

    /**
     * Prepare children blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /**
         * Create child blocks for payment methods forms
         */
        foreach ($this->getMethods() as $method) {
            $this->setChild(
               'payment.method.'.$method->getCode(),
               $this->_paymentHelper->getMethodFormBlock($method)
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method->isApplicableToQuote($this->getQuote(), AbstractMethod::CHECK_USE_FOR_COUNTRY
            | AbstractMethod::CHECK_USE_FOR_CURRENCY
            | AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX
        );
    }

    /**
     * Check and prepare payment method model
     *
     * Redeclare this method in child classes for declaring method info instance
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return $this
     */
    protected function _assignMethod($method)
    {
        $method->setInfoInstance($this->getQuote()->getPayment());
        return $this;
    }

    /**
     * Declare template for payment method form block
     *
     * @param string $method
     * @param string $template
     * @return $this
     */
    public function setMethodFormTemplate($method='', $template='')
    {
        if (!empty($method) && !empty($template)) {
            if ($block = $this->getChildBlock('payment.method.'.$method)) {
                $block->setTemplate($template);
            }
        }
        return $this;
    }

    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if ($methods === null) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = array();
            foreach ($this->_paymentHelper->getStoreMethods($store, $quote) as $method) {
                if ($this->_canUseMethod($method) && $method->isApplicableToQuote(
                    $quote,
                    AbstractMethod::CHECK_ZERO_TOTAL
                )) {
                    $this->_assignMethod($method);
                    $methods[] = $method;
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }

    /**
     * Retrieve code of current payment method
     *
     * @return string|false
     */
    public function getSelectedMethodCode()
    {
        $methods = $this->getMethods();
        if (!empty($methods)) {
            reset($methods);
            return current($methods)->getCode();
        }
        return false;
    }
}
