<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper pool
 */
class Magento_CustomerSegment_Model_Resource_HelperPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new resource helper object
     *
     * @return Magento_Core_Model_Resource_Helper_Abstract
     * @throws InvalidArgumentException
     */
    public function create()
    {
        $connectionModel = Magento_Core_Model_ObjectManager::getInstance()
            ->get('Magento_Core_Model_Config_Resource')
            ->getResourceConnectionModel('core');

        /** @var Magento_Core_Model_Registry $registryObject */
        $registryObject = $this->_objectManager->get('Magento_Core_Model_Registry');

        $registryKey = 'resourceHelper/CustomerSegment';
        if (!$registryObject->registry($registryKey)) {
            /** @var Magento_Core_Model_Resource_Helper_Abstract $resourceHelper */
            $resourceHelper = $this->_objectManager->create(
                'Magento_CustomerSegment_Model_Resource_Helper_' . ucfirst($connectionModel),
                array(
                    'modulePrefix' => 'CustomerSegment',
                )
            );
            if (false == ($resourceHelper instanceof Magento_Core_Model_Resource_Helper_Abstract)) {
                throw new InvalidArgumentException($resourceHelper
                    . ' doesn\'t extends Magento_Core_Model_Resource_Helper_Abstract'
                );
            }
            $registryObject->register($registryKey, $resourceHelper);
        }
        return $registryObject->registry($registryKey);
    }
}
