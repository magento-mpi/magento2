<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product url key attribute backend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Catalog url
     *
     * @var Magento_Catalog_Model_Url
     */
    protected $_catalogUrl;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Url $catalogUrl
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Catalog_Model_Url $catalogUrl,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_catalogUrl = $catalogUrl;
        parent::__construct($logger);
    }

    /**
     * Before save
     *
     * @param Magento_Object $object
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }

    /**
     * Refresh product rewrites
     *
     * @param Magento_Object $object
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            $this->_catalogUrl->refreshProductRewrites(null, $object, true);
        }
        return $this;
    }
}
