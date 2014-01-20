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
     * @var \Magento\Shipping\Model\Carrier\Factory
     */
    protected $_carrierFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Shipping\Model\Carrier\Factory $carrierFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Shipping\Model\Carrier\Factory $carrierFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_carrierFactory = $carrierFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = $this->_carrierFactory->create($code);
        }
        return self::$_instances[$code];
    }
}
