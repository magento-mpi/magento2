<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method\Specification;

use Magento\Multishipping\Model\Payment\Method\SpecificationInterface;

/**
 * Config specification.
 * Disable payment methods, that not allow for multishipping according to config
 */
class Config implements SpecificationInterface
{
    /**
     * Payment config
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $config;

    /**#@+
     * Config elements
     */
    const METHOD_DISABLED = 'deny_multiple_address';
    const METHOD_3DSECURE_DISABLED = 'deny_multiple_address_if3dsecure';
    /**#@-*/

    /**
     * Payment methods info
     *
     * @var array
     */
    protected $methodsInfo = array();

    /**
     * Init config
     *
     * @param \Magento\Payment\Model\Config $config
     */
    public function __construct(\Magento\Payment\Model\Config $config)
    {
        $this->config = $config;
        $this->initPaymentMethods();
    }

    /**
     * Is payment methods specification satisfied for multishipping
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSatisfiedBy($paymentMethod)
    {
        if ($this->isMethodNotSupported($paymentMethod) || $this->is3DSecureNotSupported($paymentMethod)) {
            return false;
        }
        return true;
    }

    /**
     * Init payment methods info
     */
    protected function initPaymentMethods()
    {
        $this->methodsInfo = array();
        foreach ($this->config->getMethodsInfo() as $name => $methodInfo) {
            $this->methodsInfo[$name] = $methodInfo;
        }
    }

    /**
     * Is payment method not supported for multishipping
     *
     * @param string $paymentMethod
     * @return bool
     */
    protected function isMethodNotSupported($paymentMethod)
    {
        return isset($this->methodsInfo[$paymentMethod][self::METHOD_DISABLED])
            && $this->methodsInfo[$paymentMethod][self::METHOD_DISABLED];
    }

    /**
     * Is payment method not supported for multishipping if 3DSecure enabled
     *
     * @param string $paymentMethod
     * @return bool
     */
    protected function is3DSecureNotSupported($paymentMethod)
    {
        return isset($this->methodsInfo[$paymentMethod][self::METHOD_3DSECURE_DISABLED])
            && $this->methodsInfo[$paymentMethod][self::METHOD_3DSECURE_DISABLED];
    }
}
