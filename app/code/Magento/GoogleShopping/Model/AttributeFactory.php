<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attributes Factory
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_AttributeFactory
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * GoogleShopping data
     *
     * @var Magento_GoogleShopping_Helper_Data
     */
    protected $_gsData;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_GoogleShopping_Helper_Data $gsData
     */
    public function __construct(Magento_ObjectManager $objectManager, Magento_GoogleShopping_Helper_Data $gsData)
    {
        $this->_objectManager = $objectManager;
        $this->_gsData = $gsData;
    }

    /**
     * Create attribute model
     *
     * @param string $name
     * @return Magento_GoogleShopping_Model_Attribute_Default
     */
    public function createAttribute($name)
    {
        $modelName = 'Magento_GoogleShopping_Model_Attribute_' . uc_words($this->_gsData->normalizeName($name));
        try {
            /** @var Magento_GoogleShopping_Model_Attribute_Default $attributeModel */
            $attributeModel = $this->_objectManager->create($modelName);
            if (!$attributeModel) {
                $attributeModel = $this->_objectManager->create('Magento_GoogleShopping_Model_Attribute_Default');
            }
        } catch (Exception $e) {
            $attributeModel = $this->_objectManager->create('Magento_GoogleShopping_Model_Attribute_Default');
        }

        $attributeModel->setName($name);
        return $attributeModel;
    }

    /**
     * Create attribute model
     *
     * @return Magento_GoogleShopping_Model_Attribute
     */
    public function create()
    {
        return $this->_objectManager->create('Magento_GoogleShopping_Model_Attribute');
    }
}
