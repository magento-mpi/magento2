<?php
/**
 * CatalogSearch Fulltext Index resource model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_Resource_Fulltext extends Mage_CatalogSearch_Model_Resource_Fulltext
{
    /**
     * Init search engine
     */
    public function __construct(
        Mage_Core_Model_Resource $resource,
        Mage_CatalogSearch_Model_Resource_Fulltext_Engine $engine
    ) {
        parent::__construct($resource);
        $this->_engine = $engine;
    }

    /**
     * Retrieve searchable attributes
     *
     * @param string $backendType
     * @return array
     */
    protected function _getSearchableAttributes($backendType = null)
    {
        if (is_null($this->_searchableAttributes)) {
            $this->_searchableAttributes = array();

            $productAttributeCollection = Mage::getResourceModel(
                'Mage_Catalog_Model_Resource_Product_Attribute_Collection'
            );

            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $productAttributeCollection->addToIndexFilter(true);
            } else {
                $productAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $productAttributeCollection->getItems();
            $entity = $this->getEavConfig()
                ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
                ->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }

            $this->_searchableAttributes = $attributes;
        }

        if (!is_null($backendType)) {
            $attributes = array();
            foreach ($this->_searchableAttributes as $attributeId => $attribute) {
                if ($attribute->getBackendType() == $backendType) {
                    $attributes[$attributeId] = $attribute;
                }
            }

            return $attributes;
        }

        return $this->_searchableAttributes;
    }
}
