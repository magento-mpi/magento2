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
     * Product Attribute Collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected $_productAttributeCollection;

    /**
     * Init search engine
     */
    public function __construct(
        Mage_Core_Model_Resource $resource,
        Mage_CatalogSearch_Model_Resource_Fulltext_Engine $engine,
        Mage_Catalog_Model_Resource_Product_Attribute_Collection $productAttributeCollection
    ) {
        parent::__construct($resource);
        $this->_engine = $engine;
        $this->_productAttributeCollection = $productAttributeCollection;
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

            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $this->_productAttributeCollection->addToIndexFilter(true);
            } else {
                $this->_productAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $this->_productAttributeCollection->getItems();
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
