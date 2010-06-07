<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search facets resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Resource_Facets_Attribute extends Enterprise_Search_Model_Resource_Abstract
{
    /**
     * Init main table
     *
     */
    protected function _construct()
    {

    }

    /**
     * Retrieve count products for attribute filter filter
     *
     * @param object $attribute
     * @return array
     */
    public function getCount($attribute)
    {
        $attribute = $attribute->getAttributeModel();
        $params = array();

        $params['facet'] = array(
            'field'  => $this->getAttributeSolrFieldName($attribute),
            'values' => array()
        );

        $productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        $facets = $productCollection->getFacets($params);

        $facet = !empty($facets[$params['facet']['field']]) ? $facets[$params['facet']['field']] : array();

        $resultFacet = array();
        $options = $attribute->getFrontend()->getSelectOptions();
        foreach ($options as $option) {
            $optionLabel = $this->_prepareOptionLabel($option['label']);
            if (isset($facet[$optionLabel])) {
                $resultFacet[$option['value']]=$facet[$optionLabel];
            }
        }

        return $resultFacet;
    }

    /**
     * Apply attribute filter to solr query
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     */
    public function applyFilterToCollection($filter, $value)
    {
        if (empty($value)) {
            $value = array();
        } else if (!is_array($value)) {
            $value = array($value);
        }

        $productCollection = Mage::getSingleton('catalog/layer')->getProductCollection();
        $attribute  = $filter->getAttributeModel();
        $this->addSearchQfFilter($productCollection, $attribute, $value);
    }

    /**
     * Add filter by indexable attribute
     *
     * @param Enterprise_Search_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     *
     * @return bool
     */
    public function addSearchQfFilter($collection, $attribute, $value)
    {
        $param = $this->_getSearchParam($collection, $attribute, $value);
        $collection->addSearchQfFilter($param);
        return true;
    }

    /**
     * Add filter by indexable attribute
     *
     * @param Enterprise_Search_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     *
     * @return bool
     */
    public function getAttributeSolrFieldName($attribute)
    {
        $languageCode = $this->_getLanguageCodeByLocaleCode(
            Mage::app()->getStore()
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        $field = $attribute->getAttributeCode();
        $fieldType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        }
        elseif ($fieldType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        }
        elseif (in_array($fieldType, $this->_textFieldTypes)) {
            $field .= $languageSuffix;
        }

        return $field;
    }

    /**
     * Prepare attribute option label for query
     *
     * @param string $label
     * @return string
     */
    protected function _prepareOptionLabel($label)
    {
        return strtolower(str_replace(':', '', $label));
    }
}
