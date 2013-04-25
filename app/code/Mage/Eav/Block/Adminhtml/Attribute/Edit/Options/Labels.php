<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute add/edit form options tab
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Labels extends Mage_Backend_Block_Template
{
    /** @var Mage_Core_Model_StoreManager */
    protected $_storeManager;

    /** @var Mage_Core_Model_Registry */
    protected $_registry;

    /**
     * @inheritdoc
     */
    protected $_template = 'Mage_Adminhtml::catalog/product/attribute/labels.phtml';

    /**
     * @inheritdoc
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param Mage_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_StoreManager $storeManager,
        Mage_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores());
        }
        return $this->_getData('stores');
    }

    /**
     * Retrieve attribute option values if attribute input type select or multiselect
     *
     * @return array
     */
    public function getOptionValues()
    {
        $values = $this->_getData('option_values');
        if ($values === null) {
            $values = array();

            $attribute = $this->getAttributeObject();
            $optionCollection = $this->_getOptionValuesCollection($attribute);
            if ($optionCollection) {
                $values = $this->_prepareOptionValues($attribute, $optionCollection);
            }

            $this->setData('option_values', $values);
        }

        return $values;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeObject()
    {
        return $this->_registry->registry('entity_attribute');
    }
}
