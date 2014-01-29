<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method\Specification;

use Magento\Payment\Model\Method\SpecificationInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Core\Model\Store\ConfigInterface as StoreConfig;

/**
 * Multishipping specification.
 * Check payment methods, that not allow for multishipping
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
     * Store config
     *
     * @var StoreConfig
     */
    protected $storeConfig;

    /**
     * Payment methods info
     *
     * @var array
     */
    protected $methodsInfo = array();

    /**
     * Construct
     *
     * @param PaymentConfig $paymentConfig
     * @param StoreConfig $storeConfig
     */
    public function __construct(
        PaymentConfig $paymentConfig,
        StoreConfig $storeConfig
    ) {
        $this->methodsInfo = $paymentConfig->getMethodsInfo();
        $this->storeConfig = $storeConfig;
    }

    /**
     * Is payment methods specification satisfied for multishipping
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSatisfiedBy($paymentMethod)
    {
        if ($this->isMethodSupported($paymentMethod) && $this->is3DSecureRuleSatisfiedBy($paymentMethod)) {
            return true;
        }
        return false;
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
     * Is 3D Secure rule satisfied by payment method
     *
     * @param string $paymentMethod
     * @return bool
     */
    protected function is3DSecureRuleSatisfiedBy($paymentMethod)
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
