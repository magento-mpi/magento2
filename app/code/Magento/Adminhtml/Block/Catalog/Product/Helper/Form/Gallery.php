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
 * Catalog product gallery attribute
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery extends Magento_Data_Form_Element_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
    }

    public function getElementHtml()
    {
        $html = $this->getContentHtml();
        return $html;
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {

        /* @var $content Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content */
        $content = $this->_layout->createBlock('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content');
        $content->setId($this->getHtmlId() . '_content')->setElement($this);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMegiaGallery($galleryJs);
        return $content->toHtml();
    }

    public function getLabel()
    {
        return '';
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @return bool
     */
    public function canDisplayUseDefault($attribute)
    {
        if (!$attribute->isScopeGlobal() && $this->getDataObject()->getStoreId()) {
            return true;
        }

        return false;
    }

    /**
     * Check default value usage fact
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @return bool
     */
    public function usedDefault($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($attributeCode);

        if (!$this->getDataObject()->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } else if ($this->getValue() == $defaultValue &&
                   $this->getDataObject()->getStoreId() != $this->_getDefaultStoreId()) {
            return false;
        }
        if ($defaultValue === false && !$attribute->getIsRequired() && $this->getValue()) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    public function getScopeLabel($attribute)
    {
        $html = '';
        if ($this->_storeManager->isSingleStoreMode()) {
            return $html;
        }

        if ($attribute->isScopeGlobal()) {
            $html .= '<br/>' . __('[GLOBAL]');
        } elseif ($attribute->isScopeWebsite()) {
            $html .= '<br/>' . __('[WEBSITE]');
        } elseif ($attribute->isScopeStore()) {
            $html .= '<br/>' . __('[STORE VIEW]');
        }
        return $html;
    }

    /**
     * Retrieve data object related with form
     *
     * @return Magento_Catalog_Model_Product || Magento_Catalog_Model_Category
     */
    public function getDataObject()
    {
        return $this->getForm()->getDataObject();
    }

    /**
     * Retrieve attribute field name
     *
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    public function getAttributeFieldName($attribute)
    {
        $name = $attribute->getAttributeCode();
        if ($suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        }
        return $name;
    }

    /**
     * Check readonly attribute
     *
     * @param Magento_Eav_Model_Entity_Attribute|string $attribute
     * @return boolean
     */
    public function getAttributeReadonly($attribute)
    {
        if (is_object($attribute)) {
            $attribute = $attribute->getAttributeCode();
        }

        if ($this->getDataObject()->isLockedAttribute($attribute)) {
            return true;
        }

        return false;
    }

    public function toHtml()
    {
        return '<tr><td class="value" colspan="3">' . $this->getElementHtml() . '</td></tr>';
    }

    /**
     * Default sore ID getter
     *
     * @return integer
     */
    protected function _getDefaultStoreId()
    {
        return Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }
}
