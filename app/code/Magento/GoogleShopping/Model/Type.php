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
 * Google Content Item Types Model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Type extends Magento_Core_Model_Abstract
{
    /**
     * Mapping attributes collection
     *
     * @var Magento_GoogleShopping_Model_Resource_Attribute_Collection
     */
    protected $_attributesCollection;

    /**
     * @var Magento_GoogleShopping_Helper_Product
     */
    protected $_gsProduct;

    /**
     * @var Magento_GoogleShopping_Helper_Data
     */
    protected $_gsData;

    /**
     * Config
     *
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * Attribute factory
     *
     * @var Magento_GoogleShopping_Model_AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * Attribute collection factory
     *
     * @var Magento_GoogleShopping_Model_Resource_Attribute_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_GoogleShopping_Model_Resource_Attribute_CollectionFactory $collectionFactory
     * @param Magento_GoogleShopping_Model_AttributeFactory $attributeFactory
     * @param Magento_GoogleShopping_Model_Config $config
     * @param Magento_GoogleShopping_Helper_Product $gsProduct
     * @param Magento_GoogleShopping_Helper_Data $gsData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_GoogleShopping_Model_Resource_Type $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_GoogleShopping_Model_Resource_Attribute_CollectionFactory $collectionFactory,
        Magento_GoogleShopping_Model_AttributeFactory $attributeFactory,
        Magento_GoogleShopping_Model_Config $config,
        Magento_GoogleShopping_Helper_Product $gsProduct,
        Magento_GoogleShopping_Helper_Data $gsData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_GoogleShopping_Model_Resource_Type $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_config = $config;
        $this->_gsProduct = $gsProduct;
        $this->_gsData = $gsData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_GoogleShopping_Model_Resource_Type');
    }

    /**
     * Load type model by Attribute Set Id and Target Country
     *
     * @param int $attributeSetId Attribute Set
     * @param string $targetCountry Two-letters country ISO code
     * @return Magento_GoogleShopping_Model_Type
     */
    public function loadByAttributeSetId($attributeSetId, $targetCountry)
    {
        return $this->getResource()
            ->loadByAttributeSetIdAndTargetCountry($this, $attributeSetId, $targetCountry);
    }

    /**
     * Prepare Entry data and attributes before saving in Google Content
     *
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertProductToEntry($product, $entry)
    {
        $map = $this->_getAttributesMapByProduct($product);
        $base = $this->_getBaseAttributes();
        $attributes = array_merge($base, $map);

        $this->_removeNonexistentAttributes($entry, array_keys($attributes));

        foreach ($attributes as $name => $attribute) {
            $attribute->convertAttribute($product, $entry);
        }

        return $entry;
    }

    /**
     * Return Product attribute values array
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array Product attribute values
     */
    protected function _getAttributesMapByProduct(Magento_Catalog_Model_Product $product)
    {
        $result = array();
        $group = $this->_config->getAttributeGroupsFlat();
        foreach ($this->_getAttributesCollection() as $attribute) {
            $productAttribute = $this->_gsProduct->getProductAttribute($product, $attribute->getAttributeId());

            if (!is_null($productAttribute)) {
                // define final attribute name
                if ($attribute->getGcontentAttribute()) {
                    $name = $attribute->getGcontentAttribute();
                } else {
                    $name = $this->_gsProduct->getAttributeLabel($productAttribute, $product->getStoreId());
                }

                if (!is_null($name)) {
                    $name = $this->_gsData->normalizeName($name);
                    if (isset($group[$name])) {
                        // if attribute is in the group
                        if (!isset($result[$group[$name]])) {
                            $result[$group[$name]] = $this->_attributeFactory->createAttribute($group[$name]);
                        }
                        // add group attribute to parent attribute
                        $result[$group[$name]]->addData(array(
                            'group_attribute_' . $name => $this->_attributeFactory->createAttribute($name)
                                ->addData($attribute->getData())
                        ));
                        unset($group[$name]);
                    } else {
                        if (!isset($result[$name])) {
                            $result[$name] = $this->_attributeFactory->createAttribute($name);
                        }
                        $result[$name]->addData($attribute->getData());
                    }
                }
            }
        }

        return $this->_initGroupAttributes($result);
    }

    /**
     * Retrun array with base attributes
     *
     * @return array
     */
    protected function _getBaseAttributes()
    {
        $names = $this->_config->getBaseAttributes();
        $attributes = array();
        foreach ($names as $name) {
            $attributes[$name] = $this->_attributeFactory->createAttribute($name);
        }

        return $this->_initGroupAttributes($attributes);
    }

    /**
     * Append to attributes array subattribute's models
     *
     * @param array $attributes
     * @return array
     */
    protected function _initGroupAttributes($attributes)
    {
        $group = $this->_config->getAttributeGroupsFlat();
        foreach ($group as $child => $parent) {
            if (isset($attributes[$parent]) &&
                !isset($attributes[$parent]['group_attribute_' . $child])) {
                    $attributes[$parent]->addData(
                        array('group_attribute_' . $child => $this->_attributeFactory->createAttribute($child))
                    );
            }
        }

        return $attributes;
    }

    /**
     * Retrieve type's attributes collection
     * It is protected, because only Type knows about its attributes
     *
     * @return Magento_GoogleShopping_Model_Resource_Attribute_Collection
     */
    protected function _getAttributesCollection()
    {
        if (is_null($this->_attributesCollection)) {
            $this->_attributesCollection = $this->_collectionFactory->create()
                ->addAttributeSetFilter($this->getAttributeSetId(), $this->getTargetCountry());
        }
        return $this->_attributesCollection;
    }

    /**
     * Remove attributes which were removed from mapping.
     *
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @param array $existAttributes
     * @return Magento_Gdata_Gshopping_Entry
     */
    protected function _removeNonexistentAttributes($entry, $existAttributes)
    {
        // attributes which can't be removed
        $ignoredAttributes = array(
            "id",
            "image_link",
            "content_language",
            "target_country",
            "expiration_date",
            "adult"
        );

        $contentAttributes = $entry->getContentAttributes();
        foreach ($contentAttributes as $contentAttribute) {
            $name = $this->_gsData->normalizeName($contentAttribute->getName());
            if (!in_array($name, $ignoredAttributes) &&
                !in_array($existAttributes, $existAttributes)) {
                    $entry->removeContentAttribute($name);
            }
        }

        return $entry;
    }
}
