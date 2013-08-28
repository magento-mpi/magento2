<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces layout file list instances
 */
class Magento_Core_Model_Layout_File_List_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file list
     *
     * @return Magento_Core_Model_Layout_File_List
     */
    public function create()
    {
        return $this->_objectManager->create('Magento_Core_Model_Layout_File_List');
    }
}
