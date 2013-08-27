<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product form gallery content
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Magento_Data_Form_Element_Abstract getElement()
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content extends Magento_Adminhtml_Block_Widget
{
    protected $_template = 'catalog/product/helper/gallery.phtml';

    public function __construct(Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('uploader', 'Magento_Adminhtml_Block_Media_Uploader');

        $this->getUploader()->getConfig()
            ->setUrl(
                Mage::getModel('Magento_Backend_Model_Url')
                    ->addSessionParam()
                    ->getUrl('adminhtml/catalog_product_gallery/upload')
            )
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                )
            ));

        Mage::dispatchEvent('catalog_product_gallery_prepare_layout', array('block' => $this));

        return parent::_prepareLayout();
    }


    /**
     * Retrive uploader block
     *
     * @return Magento_Adminhtml_Block_Media_Uploader
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    public function getImagesJson()
    {
        if (is_array($this->getElement()->getValue())) {
            $value = $this->getElement()->getValue();
            if (is_array($value['images']) && count($value['images']) > 0) {
                foreach ($value['images'] as &$image) {
                    $image['url'] = Mage::getSingleton('Magento_Catalog_Model_Product_Media_Config')
                        ->getMediaUrl($image['file']);
                }
                return $this->_coreData->jsonEncode($value['images']);
            }
        }
        return '[]';
    }

    public function getImagesValuesJson()
    {
        $values = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Magento_Eav_Model_Entity_Attribute */
            $values[$attribute->getAttributeCode()] = $this->getElement()->getDataObject()->getData(
                $attribute->getAttributeCode()
            );
        }
        return $this->_coreData->jsonEncode($values);
    }

    /**
     * Get image types data
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Magento_Eav_Model_Entity_Attribute */
            $imageTypes[$attribute->getAttributeCode()] = array(
                'code' => $attribute->getAttributeCode(),
                'value' => $this->getElement()->getDataObject()->getData($attribute->getAttributeCode()),
                'label' => $attribute->getFrontend()->getLabel(),
                'scope' => __($this->getElement()->getScopeLabel($attribute)),
                'name' => $this->getElement()->getAttributeFieldName($attribute)
            );
        }
        return $imageTypes;
    }

    public function hasUseDefault()
    {
        foreach ($this->getMediaAttributes() as $attribute) {
            if($this->getElement()->canDisplayUseDefault($attribute))  {
                return true;
            }
        }

        return false;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    public function getImageTypesJson()
    {
        return $this->_coreData->jsonEncode($this->getImageTypes());
    }

}
