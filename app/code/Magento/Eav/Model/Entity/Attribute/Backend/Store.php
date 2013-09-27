<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Backend_Store extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     */
    public function __construct(Magento_Core_Model_StoreManager $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare data before save
     *
     * @param Magento_Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Store
     */
    protected function _beforeSave($object)
    {
        if (!$object->getData($this->getAttribute()->getAttributeCode())) {
            $object->setData($this->getAttribute()->getAttributeCode(), $this->_storeManager->getStore()->getId());
        }

        return $this;
    }
}
