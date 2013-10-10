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
 * Category image attribute frontend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Category\Attribute\Frontend;

class Image
    extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    const IMAGE_PATH_SEGMENT = 'catalog/category/';

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * Return image url
     *
     * @param \Magento\Object $object
     * @return string|null
     */
    public function getUrl($object)
    {
        $url = false;
        if ($image = $object->getData($this->getAttribute()->getAttributeCode())) {
            $url = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_MEDIA) . self::IMAGE_PATH_SEGMENT . $image;
        }
        return $url;
    }
}
