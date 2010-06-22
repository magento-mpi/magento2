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
 * Layer attribute filter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $useInCatalogNavigation = Mage::helper('enterprise_search')->useEngineInLayeredNavigation();
        if ($useInCatalogNavigation) {
            $attribute = $this->getAttributeModel();
            $this->_requestVar = $attribute->getAttributeCode();

            $fieldName = Mage::helper('enterprise_search')->getAttributeSolrFieldName($attribute);
            $productCollection = $this->getLayer()->getProductCollection();
            $options = $productCollection->getFacetedData($fieldName);

            $data = array();
            foreach ($options as $label => $count) {
                if (Mage::helper('core/string')->strlen($label)) {
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($count)) {
                            $data[] = array(
                                'label' => $label,
                                'value' => $label,
                                'count' => $count,
                            );
                        }
                    } else {
                        $data[] = array(
                            'label' => $label,
                            'value' => $label,
                            'count' => (int) $count,
                        );
                    }
                }
            }
        } else {
            $data = parent::_getItemsData();
        }

        return $data;
    }

    /**
     * Apply attribute filter to layer
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param object $filterBlock
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $useInCatalogNavigation = Mage::helper('enterprise_search')->useEngineInLayeredNavigation();
        if ($useInCatalogNavigation) {
            $facetField = Mage::helper('enterprise_search')->getAttributeSolrFieldName($this->getAttributeModel());
            $productCollection = $this->getLayer()->getProductCollection();
            $productCollection->setFacetCondition($facetField);
        } else {
            parent::apply($request, $filterBlock);
        }

        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }

        if ($filter) {
            $text = $this->_getOptionText($filter);
            $this->applyFilterToCollection($this, $filter);
            if ($useInCatalogNavigation) {
                $this->getLayer()->getState()->addFilter($this->_createItem($filter, $filter));
            }
            $this->_items = array();
        }

        return $this;
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

        $productCollection = $this->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();

        $param = Mage::helper('enterprise_search')->getSearchParam($productCollection, $attribute, $value);
        $productCollection->addSearchQfFilter($param);
        return $this;
    }
}
