<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

/**
 * Product url key attribute backend
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Urlkey extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Catalog url
     *
     * @var \Magento\Catalog\Model\Url
     */
    protected $_catalogUrl;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Catalog\Model\Url $catalogUrl
     */
    public function __construct(\Magento\Framework\Logger $logger, \Magento\Catalog\Model\Url $catalogUrl)
    {
        $this->_catalogUrl = $catalogUrl;
        parent::__construct($logger);
    }

    /**
     * Before save
     *
     * @param \Magento\Framework\Object $object
     * @return $this
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
     * @param \Magento\Framework\Object $object
     * @return $this
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            /**
             * @TODO: UrlRewrite MAGETWO-26285
             * $this->_catalogUrl->refreshProductRewrites(null, $object, true);
             */
        }
        return $this;
    }
}
