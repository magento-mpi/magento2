<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Observer;

/**
 * Catalog Observer Reindex
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Reindex
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function fulltextReindex(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $category \Magento\Catalog\Model\Category */
        $category = $observer->getDataObject();
        if ($category && count($category->getAffectedProductIds()) > 0) {
            /** @var $resource \Magento\CatalogSearch\Model\Resource\Fulltext */
            $resource = $this->_objectManager->get('Magento\CatalogSearch\Model\Resource\Fulltext');
            $resource->rebuildIndex(null, $category->getAffectedProductIds());
        }
        return $this;
    }
}
