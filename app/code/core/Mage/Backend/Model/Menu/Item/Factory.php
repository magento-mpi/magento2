<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Item_Factory
{
    /**
     * ACL
     *
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_acl;

    /**
     * Object factory
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __constructor(array $data = array())
    {
        if (!isset($data['acl']) || !($data['acl'] instanceof Mage_Backend_Model_Auth_Session)) {
            throw new InvalidArgumentException();
        }
        $this->_acl = $data['acl'];

        if (!isset($data['objectFactory']) || !$data['objectFactory'] instanceof Mage_Core_Model_Config) {
            throw new InvalidArgumentException();
        }
        $this->_objectFactory = $data['objectFactory'];
    }

    /**
     * Create menu item from array
     *
     * @param array $data
     * @return Mage_Backend_Model_Menu_Item
     */
    public function createFromArray(array $data = array())
    {
        $module = 'Mage_Backend_Helper_Data';
        if (isset($data['module'])) {
            $module = $data['module'];
        }
        $data['module'] = Mage::helper($module);

        if (isset($data['dependsOnConfig'])) {
            $data['dependsOnConfig'] = Mage::helper($data['dependsOnModule']);
        }
        return $this->_objectFactory->getModelInstance('Mage_Backend_Model_Menu_Item', $data);
    }
}
