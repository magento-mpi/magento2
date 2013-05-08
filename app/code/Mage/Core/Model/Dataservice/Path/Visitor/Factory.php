<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_Dataservice_Path_Visitor_Factory
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
     * Get the value for the method argument
     *
     * @param $path
     * @return Mage_Core_Model_Dataservice_Path_Visitor
     */
    public function get($path) {
        /** @var $visitor Mage_Core_Model_Dataservice_Path_Visitor */
        $visitor = $this->_objectManager->create('Mage_Core_Model_Dataservice_Path_Visitor',
            array('path' => $path, 'separator' => '.'));
        return $visitor;
    }
}