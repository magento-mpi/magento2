<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item Form Model
 */
class Magento_Rma_Model_Item_Form extends Magento_Eav_Model_Form
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Magento_Rma';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'rma_item';

    /**
     * @var Magento_Rma_Model_Resource_Item_Form_Attribute_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Eav_Model_AttributeDataFactory $attrDataFactory
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Controller_Request_Http $httpRequest
     * @param Magento_Validator_ConfigFactory $validatorConfigFactory
     * @param Magento_Rma_Model_Resource_Item_Form_Attribute_CollectionFactory $collectionFactory
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Eav_Model_AttributeDataFactory $attrDataFactory,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Controller_Request_Http $httpRequest,
        Magento_Validator_ConfigFactory $validatorConfigFactory,
        Magento_Rma_Model_Resource_Item_Form_Attribute_CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct(
            $storeManager,
            $eavConfig,
            $modulesReader,
            $attrDataFactory,
            $universalFactory,
            $httpRequest,
            $validatorConfigFactory
        );
    }

    /**
     * Get EAV Entity Form Attribute Collection
     *
     * @return Magento_Rma_Model_Resource_Item_Form_Attribute_Collection
     */
    protected function _getFormAttributeCollection()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * Validate data array and return true or array of errors
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data)
    {
        $errors = array();
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getAttributeCode() == 'reason_other') {
                continue;
            }
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }
}
