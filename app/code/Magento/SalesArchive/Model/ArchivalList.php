<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Model;

/**
 * Sales archival model list
 */
class ArchivalList
{
    /**
     * Archival entity names
     */
    const ORDER = 'order';

    const INVOICE = 'invoice';

    const SHIPMENT = 'shipment';

    const CREDITMEMO = 'creditmemo';

    /**
     * Archival entities definition
     *
     * @var $_entities array
     */
    protected $_entities = [
        self::ORDER => [
            'model' => 'Magento\Sales\Model\Order',
            'resource_model' => 'Magento\Sales\Model\Resource\Order',
        ],
        self::INVOICE => [
            'model' => 'Magento\Sales\Model\Order\Invoice',
            'resource_model' => 'Magento\Sales\Model\Resource\Order\Invoice',
        ],
        self::SHIPMENT => [
            'model' => 'Magento\Sales\Model\Order\Shipment',
            'resource_model' => 'Magento\Sales\Model\Resource\Order\Shipment',
        ],
        self::CREDITMEMO => [
            'model' => 'Magento\Sales\Model\Order\Creditmemo',
            'resource_model' => 'Magento\Sales\Model\Resource\Order\Creditmemo',
        ],
    ];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get archival resource model singleton
     *
     * @param string $entity
     * @param array $arguments
     * @return \Magento\Sales\Model\Resource\Entity
     * @throws \LogicException
     */
    public function getResource($entity, array $arguments = [])
    {
        $className = $this->_getClassByEntity($entity);

        if ($className === false) {
            throw new \LogicException($entity . ' entity isn\'t allowed');
        }
        $model = $this->_objectManager->get($className, $arguments);
        return $model;
    }

    /**
     * Returns resource model class of an entity
     *
     * @param string $entity
     * @return string|bool
     */
    protected function _getClassByEntity($entity)
    {
        return isset($this->_entities[$entity]) ? $this->_entities[$entity]['resource_model'] : false;
    }

    /**
     * Return entity by object
     *
     * @param \Magento\Framework\Object $object
     * @return string|false
     */
    public function getEntityByObject($object)
    {
        $keys = ['model', 'resource_model'];
        foreach ($this->_entities as $archiveEntity => $entityClasses) {
            foreach ($keys as $key) {
                $className = $entityClasses[$key];
                if ($object instanceof $className) {
                    return $archiveEntity;
                }
            }
        }
        return false;
    }

    /**
     * Return entity names
     *
     * @return array
     */
    public function getEntityNames()
    {
        return array_keys($this->_entities);
    }
}
