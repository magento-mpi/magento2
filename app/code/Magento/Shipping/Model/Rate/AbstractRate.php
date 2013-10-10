<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Rate;

abstract class AbstractRate extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var array
     */
    static protected $_instances;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfig;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_shippingConfig = $shippingConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = $this->_shippingConfig->getCarrierInstance($code);
        }
        return self::$_instances[$code];
    }
}
