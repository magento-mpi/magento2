<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Item_Factory
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
     * @return Magento_Backend_Model_Menu_Item
     */
    public function create(array $data = array())
    {
        $module = 'Magento_Backend_Helper_Data';
        if (isset($data['module'])) {
            $module = $data['module'];
            unset($data['module']);
        }
        $data = array('data' => $data);
        $data['helper'] = $this->_helperFactory->get($module);
        return $this->_objectManager->create('Magento_Backend_Model_Menu_Item', $data);
    }
}
