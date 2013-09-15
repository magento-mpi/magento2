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
 * Product gallery
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product;

class Gallery extends \Magento\Core\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->getProduct()->getMetaTitle());
        }
        return parent::_prepareLayout();
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    public function getGalleryCollection()
    {
        return $this->getProduct()->getMediaGalleryImages();
    }

    public function getCurrentImage()
    {
        $imageId = $this->getRequest()->getParam('image');
        $image = null;
        if ($imageId) {
            $image = $this->getGalleryCollection()->getItemById($imageId);
        }

        if (!$image) {
            $image = $this->getGalleryCollection()->getFirstItem();
        }
        return $image;
    }

    public function getImageUrl()
    {
        return $this->getCurrentImage()->getUrl();
    }

    public function getImageFile()
    {
        return $this->getCurrentImage()->getFile();
    }

    /**
     * Retrieve image width
     *
     * @return bool|int
     */
    public function getImageWidth()
    {
        $file = $this->getCurrentImage()->getPath();
        if ($this->_filesystem->isFile($file)) {
            $size = getimagesize($file);
            if (isset($size[0])) {
                if ($size[0] > 600) {
                    return 600;
                } else {
                    return $size[0];
                }
            }
        }

        return false;
    }

    public function getPreviusImage()
    {
        $current = $this->getCurrentImage();
        if (!$current) {
            return false;
        }
        $previus = false;
        foreach ($this->getGalleryCollection() as $image) {
            if ($image->getValueId() == $current->getValueId()) {
                return $previus;
            }
            $previus = $image;
        }
        return $previus;
    }

    public function getNextImage()
    {
        $current = $this->getCurrentImage();
        if (!$current) {
            return false;
        }

        $next = false;
        $currentFind = false;
        foreach ($this->getGalleryCollection() as $image) {
            if ($currentFind) {
                return $image;
            }
            if ($image->getValueId() == $current->getValueId()) {
                $currentFind = true;
            }
        }
        return $next;
    }

    public function getPreviusImageUrl()
    {
        $image = $this->getPreviusImage();
        if ($image) {
            return $this->getUrl('*/*/*', array('_current' => true, 'image' => $image->getValueId()));
        }
        return false;
    }

    public function getNextImageUrl()
    {
        $image = $this->getNextImage();
        if ($image) {
            return $this->getUrl('*/*/*', array('_current' => true, 'image' => $image->getValueId()));
        }
        return false;
    }
}
