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
class Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Image extends Magento_Data_Form_Element_Image
{
    /**
     * @var Magento_Core_Model_Theme_Image_Path
     */
    protected $_imagePathModel;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_Core_Model_Theme_Image_Path $imagePathModel
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        Magento_Core_Model_Theme_Image_Path $imagePathModel,
        $attributes = array()
    ) {
        $this->_imagePathModel = $imagePathModel;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
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
            $url = $this->_imagePathModel->getPreviewImageDirectoryUrl() . $this->getValue();
        }
        return $url;
    }
}
