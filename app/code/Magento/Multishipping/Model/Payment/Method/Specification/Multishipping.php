<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method\Specification;

use Magento\Payment\Model\Method\SpecificationInterface;

/**
 * Multishipping specification.
 * Disable payment methods, that not allow for multishipping
 */
class Multishipping implements SpecificationInterface
{
    /**#@+
     * Payment config flags
     */
    const FLAG_ALLOW_MULTIPLE_ADDRESS = 'allow_multiple_address';
    const FLAG_ALLOW_MULTIPLE_WITH_3DSECURE = 'allow_multiple_with_3dsecure';
    /**#@-*/

    /**#@+
     * 3D Secure card validation store config paths
     */
    const PATH_PAYMENT_3DSECURE = 'payment/%s/enable3ds';
    const PATH_PAYMENT_CENTINEL = 'payment/%s/centinel';
    /**#@-*/

    /**
     * Payment config
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * Store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $storeConfig;

    /**
     * Payment methods info
     *
     * @var array
     */
    protected $methodsInfo = array();

    /**
     * Init config
     *
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->storeConfig = $coreStoreConfig;
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
        if ($this->isMethodSupported($paymentMethod) && $this->is3DSecureSupported($paymentMethod)) {
            return true;
        }
        return false;
    }

    /**
     * Init payment methods info
     */
    protected function initPaymentMethods()
    {
        $this->methodsInfo = $this->paymentConfig->getMethodsInfo();
    }

    /**
     * Is payment method supported for multishipping
     *
     * @param string $paymentMethod
     * @return bool
     */
    protected function isMethodSupported($paymentMethod)
    {
        return isset($this->methodsInfo[$paymentMethod][self::FLAG_ALLOW_MULTIPLE_ADDRESS])
            && $this->methodsInfo[$paymentMethod][self::FLAG_ALLOW_MULTIPLE_ADDRESS];
    }

    /**
     * Is payment method supported for multishipping if 3DSecure enabled
     *
     * @param string $paymentMethod
     * @return bool
     */
    protected function is3DSecureSupported($paymentMethod)
    {
        $is3DSecureSupported = isset($this->methodsInfo[$paymentMethod][self::FLAG_ALLOW_MULTIPLE_WITH_3DSECURE])
            && $this->methodsInfo[$paymentMethod][self::FLAG_ALLOW_MULTIPLE_WITH_3DSECURE];
        return $is3DSecureSupported || !$this->is3DSecureEnabled($paymentMethod);
    }

    /**
     * Is 3DSecure enabled for payment method
     *
     * @param string $paymentMethod
     * @return bool
     */
    protected function is3DSecureEnabled($paymentMethod)
    {
        return $this->storeConfig->getConfigFlag(sprintf(self::PATH_PAYMENT_3DSECURE, $paymentMethod))
            || $this->storeConfig->getConfigFlag(sprintf(self::PATH_PAYMENT_CENTINEL, $paymentMethod));
    }
}
