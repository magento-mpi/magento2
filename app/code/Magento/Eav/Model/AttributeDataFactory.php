<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Data Factory
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_AttributeDataFactory
{
    const OUTPUT_FORMAT_JSON    = 'json';
    const OUTPUT_FORMAT_TEXT    = 'text';
    const OUTPUT_FORMAT_HTML    = 'html';
    const OUTPUT_FORMAT_PDF     = 'pdf';
    const OUTPUT_FORMAT_ONELINE = 'oneline';
    const OUTPUT_FORMAT_ARRAY   = 'array'; // available only for multiply attributes

    /**
     * Array of attribute data models by input type
     *
     * @var array
     */
    protected $_dataModels = array();

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
     * Return attribute data model by attribute
     * Set entity to data model (need for work)
     *
     * @param Magento_Eav_Model_Attribute $attribute
     * @param Magento_Core_Model_Abstract $entity
     * @return Magento_Eav_Model_Attribute_Data_Abstract
     */
    public function create(Magento_Eav_Model_Attribute $attribute, Magento_Core_Model_Abstract $entity)
    {
        /* @var $dataModel Magento_Eav_Model_Attribute_Data_Abstract */
        $dataModelClass = $attribute->getDataModel();
        if (!empty($dataModelClass)) {
            if (empty($this->_dataModels[$dataModelClass])) {
                $dataModel = $this->_objectManager->create($dataModelClass);
                $this->_dataModels[$dataModelClass] = $dataModel;
            } else {
                $dataModel = $this->_dataModels[$dataModelClass];
            }
        } else {
            if (empty($this->_dataModels[$attribute->getFrontendInput()])) {
                $dataModelClass = sprintf(
                    'Magento_Eav_Model_Attribute_Data_%s',
                    uc_words($attribute->getFrontendInput())
                );
                $dataModel = $this->_objectManager->create($dataModelClass);
                $this->_dataModels[$attribute->getFrontendInput()] = $dataModel;
            } else {
                $dataModel = $this->_dataModels[$attribute->getFrontendInput()];
            }
        }

        $dataModel->setAttribute($attribute);
        $dataModel->setEntity($entity);

        return $dataModel;
    }
}
