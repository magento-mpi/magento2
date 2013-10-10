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
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

class Urlkey
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Catalog url
     *
     * @var \Magento\Catalog\Model\Url
     */
    protected $_catalogUrl;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Url $catalogUrl
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Catalog\Model\Url $catalogUrl,
        \Magento\Core\Model\Logger $logger
    ) {
        $this->_catalogUrl = $catalogUrl;
        parent::__construct($logger);
    }

    /**
     * Before save
     *
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Urlkey
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
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Urlkey
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            $this->_catalogUrl->refreshProductRewrites(null, $object, true);
        }
        return $this;
    }
}
