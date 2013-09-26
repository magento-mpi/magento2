<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export EAV entity abstract model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_ImportExport_Model_Export_Entity_EavAbstract
    extends Magento_ImportExport_Model_Export_EntityAbstract
{
    /**
     * Attribute code to its values. Only attributes with options and only default store values used
     *
     * @var array
     */
    protected $_attributeValues = array();

    /**
     * Attribute code to its values. Only attributes with options and only default store values used
     *
     * @var array
     */
    protected $_attributeCodes = null;

    /**
     * Entity type id.
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Attributes with index (not label) value
     *
     * @var array
     */
    protected $_indexValueAttributes = array();

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array();

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_App $app
     * @param Magento_ImportExport_Model_Export_Factory $collectionFactory
     * @param Magento_ImportExport_Model_Resource_CollectionByPagesIteratorFactory $resourceColFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Eav_Model_Config $eavConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_App $app,
        Magento_ImportExport_Model_Export_Factory $collectionFactory,
        Magento_ImportExport_Model_Resource_CollectionByPagesIteratorFactory $resourceColFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Eav_Model_Config $eavConfig,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($coreStoreConfig, $app, $collectionFactory, $resourceColFactory, $data);

        if (isset($data['entity_type_id'])) {
            $this->_entityTypeId = $data['entity_type_id'];
        } else {
            $this->_entityTypeId = $eavConfig->getEntityType($this->getEntityTypeCode())->getEntityTypeId();
        }
    }

    /**
     * Get attributes codes which are appropriate for export
     *
     * @return array
     */
    protected function _getExportAttributeCodes()
    {
        if (null === $this->_attributeCodes) {
            if (!empty($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP])
                && is_array($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP])) {
                $skippedAttributes = array_flip(
                    $this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP]
                );
            } else {
                $skippedAttributes = array();
            }
            $attributeCodes = array();

            /** @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */
            foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
                if (!isset($skippedAttributes[$attribute->getAttributeId()])
                    || in_array($attribute->getAttributeCode(), $this->_permanentAttributes)) {
                    $attributeCodes[] = $attribute->getAttributeCode();
                }
            }
            $this->_attributeCodes = $attributeCodes;
        }
        return $this->_attributeCodes;
    }

    /**
     * Initialize attribute option values
     *
     * @return Magento_ImportExport_Model_Export_Entity_EavAbstract
     */
    protected function _initAttributeValues()
    {
        /** @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */
        foreach ($this->getAttributeCollection() as $attribute) {
            $this->_attributeValues[$attribute->getAttributeCode()] = $this->getAttributeOptions($attribute);
        }
        return $this;
    }

    /**
     * Apply filter to collection and add not skipped attributes to select
     *
     * @param Magento_Eav_Model_Entity_Collection_Abstract $collection
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _prepareEntityCollection(Magento_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $this->filterEntityCollection($collection);
        $this->_addAttributesToCollection($collection);
        return $collection;
    }

    /**
     * Apply filter to collection
     *
     * @param Magento_Eav_Model_Entity_Collection_Abstract $collection
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function filterEntityCollection(Magento_Eav_Model_Entity_Collection_Abstract $collection)
    {
        if (!isset($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])
            || !is_array($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])) {
            $exportFilter = array();
        } else {
            $exportFilter = $this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP];
        }

        /** @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */
        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            // filter applying
            if (isset($exportFilter[$attributeCode])) {
                $attributeFilterType = Magento_ImportExport_Model_Export::getAttributeFilterType($attribute);

                if (Magento_ImportExport_Model_Export::FILTER_TYPE_SELECT == $attributeFilterType) {
                    if (is_scalar($exportFilter[$attributeCode]) && trim($exportFilter[$attributeCode])) {
                        $collection->addAttributeToFilter($attributeCode, array('eq' => $exportFilter[$attributeCode]));
                    }
                } elseif (Magento_ImportExport_Model_Export::FILTER_TYPE_INPUT == $attributeFilterType) {
                    if (is_scalar($exportFilter[$attributeCode]) && trim($exportFilter[$attributeCode])) {
                        $collection->addAttributeToFilter($attributeCode,
                            array('like' => "%{$exportFilter[$attributeCode]}%")
                        );
                    }
                } elseif (Magento_ImportExport_Model_Export::FILTER_TYPE_DATE == $attributeFilterType) {
                    if (is_array($exportFilter[$attributeCode]) && count($exportFilter[$attributeCode]) == 2) {
                        $from = array_shift($exportFilter[$attributeCode]);
                        $to   = array_shift($exportFilter[$attributeCode]);

                        if (is_scalar($from) && !empty($from)) {
                            $date = $this->_locale->date($from, null, null, false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attributeCode, array('from' => $date, 'date' => true));
                        }
                        if (is_scalar($to) && !empty($to)) {
                            $date = $this->_locale->date($to, null, null, false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attributeCode, array('to' => $date, 'date' => true));
                        }
                    }
                } elseif (Magento_ImportExport_Model_Export::FILTER_TYPE_NUMBER == $attributeFilterType) {
                    if (is_array($exportFilter[$attributeCode]) && count($exportFilter[$attributeCode]) == 2) {
                        $from = array_shift($exportFilter[$attributeCode]);
                        $to   = array_shift($exportFilter[$attributeCode]);

                        if (is_numeric($from)) {
                            $collection->addAttributeToFilter($attributeCode, array('from' => $from));
                        }
                        if (is_numeric($to)) {
                            $collection->addAttributeToFilter($attributeCode, array('to' => $to));
                        }
                    }
                }
            }
        }
        return $collection;
    }

    /**
     * Add not skipped attributes to select
     *
     * @param Magento_Eav_Model_Entity_Collection_Abstract $collection
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _addAttributesToCollection(Magento_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $attributeCodes = $this->_getExportAttributeCodes();
        $collection->addAttributeToSelect($attributeCodes);
        return $collection;
    }

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return array
     */
    public function getAttributeOptions(Magento_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $options = array();

        if ($attribute->usesSource()) {
            // should attribute has index (option value) instead of a label?
            $index = in_array($attribute->getAttributeCode(), $this->_indexValueAttributes) ? 'value' : 'label';

            // only default (admin) store values used
            $attribute->setStoreId(Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    $optionValues = is_array($option['value']) ? $option['value'] : array($option);
                    foreach ($optionValues as $innerOption) {
                        if (strlen($innerOption['value'])) { // skip ' -- Please Select -- ' option
                            $options[$innerOption['value']] = $innerOption[$index];
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore exceptions connected with source models
            }
        }
        return $options;
    }

    /**
     * Entity type ID getter
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->_entityTypeId;
    }

    /**
     * Fill row with attributes values
     *
     * @param Magento_Core_Model_Abstract $item export entity
     * @param array $row data row
     * @return array
     */
    protected function _addAttributeValuesToRow(Magento_Core_Model_Abstract $item, array $row = array())
    {
        $validAttributeCodes = $this->_getExportAttributeCodes();
        // go through all valid attribute codes
        foreach ($validAttributeCodes as $attributeCode) {
            $attributeValue = $item->getData($attributeCode);

            if (isset($this->_attributeValues[$attributeCode])
                && isset($this->_attributeValues[$attributeCode][$attributeValue])
            ) {
                $attributeValue = $this->_attributeValues[$attributeCode][$attributeValue];
            }
            if (null !== $attributeValue) {
                $row[$attributeCode] = $attributeValue;
            }
        }

        return $row;
    }
}
