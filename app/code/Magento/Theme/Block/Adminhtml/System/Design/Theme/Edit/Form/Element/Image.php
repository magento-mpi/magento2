<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image form element that generates correct thumbnail image URL for theme preview image
 */
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element;

class Image extends \Magento\Data\Form\Element\Image
{
    /**
     * @var \Magento\Core\Model\Theme\Image\Path
     */
    protected $_imagePath;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Theme_Image_Path $imagePath
     * @param array $attributes
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        Magento_Core_Model_UrlInterface $urlBuilder,
        \Magento\Core\Model\Theme\Image\Path $imagePath,
        $attributes = array()
    ) {
        $this->_imagePath = $imagePath;
        parent::__construct(
            $coreData,
            $factoryElement,
            $factoryCollection,
            $urlBuilder,
            $attributes
        );
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->_imagePath->getPreviewImageDirectoryUrl() . $this->getValue();
        }
        return $url;
    }
}
