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
 * Catalog Observer Reindex
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Observer_Reindex
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Reindex fulltext
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Catalog_Model_Observer_Reindex
     */
    public function fulltextReindex(\Magento\Event\Observer $observer)
    {
        /** @var $category Magento_Catalog_Model_Category */
        $category = $observer->getDataObject();
        if ($category && count($category->getAffectedProductIds()) > 0) {
            /** @var $resource Magento_CatalogSearch_Model_Resource_Fulltext */
            $resource = $this->_objectManager->get('Magento_CatalogSearch_Model_Resource_Fulltext');
            $resource->rebuildIndex(null, $category->getAffectedProductIds());
        }
        return $this;
    }
}
