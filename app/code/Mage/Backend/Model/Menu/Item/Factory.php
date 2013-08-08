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
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(Magento_ObjectManager $objectManager, Magento_Core_Model_Factory_Helper $helperFactory)
    {
        $this->_objectManager = $objectManager;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Create menu item from array
     *
     * @param array $data
     * @return Mage_Backend_Model_Menu_Item
     */
    public function create(array $data = array())
    {
        $module = 'Mage_Backend_Helper_Data';
        if (isset($data['module'])) {
            $module = $data['module'];
            unset($data['module']);
        }
        $data = array('data' => $data);
        $data['helper'] = $this->_helperFactory->get($module);
        return $this->_objectManager->create('Mage_Backend_Model_Menu_Item', $data);
    }
}
