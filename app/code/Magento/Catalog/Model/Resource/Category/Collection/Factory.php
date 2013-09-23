<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Resource_Category_Collection_Factory
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
     * Return newly created instance of the category collection
     *
     * @return Magento_Catalog_Model_Resource_Category_Collection
     */
    public function create()
    {
        return $this->_objectManager->create('Magento_Catalog_Model_Resource_Category_Collection');
    }
}
