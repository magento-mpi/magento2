<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archival model list
 */
class Magento_SalesArchive_Model_ArchivalList
{
    /**
     * Archival entity names
     */
    const ORDER      = 'order';
    const INVOICE    = 'invoice';
    const SHIPMENT   = 'shipment';
    const CREDITMEMO = 'creditmemo';

    /**
     * Archival entities definition
     *
     * @var $_entities array
     */
    protected $_entities = array(
        self::ORDER => array(
            'model' => 'Magento_Sales_Model_Order',
            'resource_model' => 'Magento_Sales_Model_Resource_Order'
        ),
        self::INVOICE => array(
            'model' => 'Magento_Sales_Model_Order_Invoice',
            'resource_model' => 'Magento_Sales_Model_Resource_Order_Invoice'
        ),
        self::SHIPMENT  => array(
            'model' => 'Magento_Sales_Model_Order_Shipment',
            'resource_model' => 'Magento_Sales_Model_Resource_Order_Shipment'
        ),
        self::CREDITMEMO => array(
            'model' => 'Magento_Sales_Model_Order_Creditmemo',
            'resource_model' => 'Magento_Sales_Model_Resource_Order_Creditmemo'
        )
    );

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get archival resource model singleton
     *
     * @param string $entity
     * @param array $arguments
     * @return Magento_Sales_Model_Resource_Order_Abstract
     * @throws LogicException
     */
    public function getResource($entity, array $arguments = array())
    {
        $className = $this->_getClassByEntity($entity);

        if ($className === false) {
            throw new LogicException(
                $entity . ' entity isn\'t allowed'
            );
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
    public function _getClassByEntity($entity)
    {
        return isset($this->_entities[$entity]) ? $this->_entities[$entity]['resource_model'] : false;
    }

    /**
     * Return entity by object
     *
     * @param Magento_Object $object
     * @return string|boolean
     */
    public function getEntityByObject($object)
    {
        $keys = array('model', 'resource_model');
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
}
