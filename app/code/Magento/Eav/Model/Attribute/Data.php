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
class Magento_Eav_Model_Attribute_Data
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
    protected static $_dataModels   = array();

    /**
     * Return attribute data model by attribute
     * Set entity to data model (need for work)
     *
     * @param Magento_Eav_Model_Attribute $attribute
     * @param Magento_Core_Model_Abstract $entity
     * @return Magento_Eav_Model_Attribute_Data_Abstract
     */
    public static function factory(Magento_Eav_Model_Attribute $attribute, Magento_Core_Model_Abstract $entity)
    {
        /* @var $dataModel Magento_Eav_Model_Attribute_Data_Abstract */
        $dataModelClass = $attribute->getDataModel();
        if (!empty($dataModelClass)) {
            if (empty(self::$_dataModels[$dataModelClass])) {
                $dataModel = Mage::getModel($dataModelClass);
                self::$_dataModels[$dataModelClass] = $dataModel;
            } else {
                $dataModel = self::$_dataModels[$dataModelClass];
            }
        } else {
            if (empty(self::$_dataModels[$attribute->getFrontendInput()])) {
                $dataModelClass = sprintf('Magento_Eav_Model_Attribute_Data_%s', uc_words($attribute->getFrontendInput()));
                $dataModel      = Mage::getModel($dataModelClass);
                self::$_dataModels[$attribute->getFrontendInput()] = $dataModel;
            } else {
                $dataModel = self::$_dataModels[$attribute->getFrontendInput()];
            }
        }

        $dataModel->setAttribute($attribute);
        $dataModel->setEntity($entity);

        return $dataModel;
    }
}
