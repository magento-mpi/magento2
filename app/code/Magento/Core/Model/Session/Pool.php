<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Session_Pool
{
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
     * Create instance
     *
     * @param string $instanceName
     * @param array $data
     * @throws LogicException
     * @return Magento_Core_Model_Session_Abstract
     */
    public function get($instanceName, $data = array())
    {
        $object = $this->_objectManager->get($instanceName, array('data' => $data));
        if (!$object instanceof Magento_Core_Model_Session_Abstract) {
            throw new LogicException($instanceName . ' doesn\'t extend Magento_Core_Model_Session_Abstract');
        }

        return $object;
    }
}
