<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method\Specification;

use Magento\Payment\Model\Method\Specification\AbstractSpecification;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Core\Model\Store\ConfigInterface as StoreConfig;

/**
 * 3D secure specification
 */
class Is3DSecure extends AbstractSpecification
{
    /**
     * Allow multiple address with 3d secure flag
     */
    const FLAG_ALLOW_MULTIPLE_WITH_3DSECURE = 'allow_multiple_with_3dsecure';

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
     * Construct
     *
     * @param PaymentConfig $paymentConfig
     * @param StoreConfig $storeConfig
     */
    public function __construct(PaymentConfig $paymentConfig, StoreConfig $storeConfig)
    {
        parent::__construct($paymentConfig);
        $this->storeConfig = $storeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($paymentMethod)
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
