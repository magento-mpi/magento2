<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Dynamic attributes Form Block
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomAttribute_Block_Form extends Magento_Core_Block_Template
{
    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = '';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = '';

    /**
     * Array of attribute renderers data keyed by attribute front-end type
     *
     * @var array
     */
    protected $_renderBlockTypes    = array();

    /**
     * Array of renderer blocks keyed by attribute front-end type
     *
     * @var array
     */
    protected $_renderBlocks        = array();

    /**
     * EAV Form Type code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Entity model class type for new entity object
     *
     * @var string
     */
    protected $_entityModelClass;

    /**
     * Entity type instance
     *
     * @var Magento_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * EAV form instance
     *
     * @var Magento_Eav_Model_Form
     */
    protected $_form;

    /**
     * EAV Entity Model
     *
     * @var Magento_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Format for HTML elements id attribute
     *
     * @var string
     */
    protected $_fieldIdFormat   = '%1$s';

    /**
     * Format for HTML elements name attribute
     *
     * @var string
     */
    protected $_fieldNameFormat = '%1$s';

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($coreData, $context, $data);
    }


    /**
     * Add custom renderer block and template for rendering EAV entity attributes
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Magento_CustomAttribute_Block_Form
     */
    public function addRenderer($type, $block, $template)
    {
        $this->_renderBlockTypes[$type] = array(
            'block'     => $block,
            'template'  => $template,
        );

        return $this;
    }

    /**
     * Try to get EAV Form Template Block
     * Get Attribute renderers from it, and add to self
     *
     * @return Magento_CustomAttribute_Block_Form
     * @throws Magento_Core_Exception
     */
    protected function _prepareLayout()
    {
        if (empty($this->_xmlBlockName)) {
            Mage::throwException(__('The current module XML block name is undefined.'));
        }
        if (empty($this->_formModelPath)) {
            Mage::throwException(__('The current module form model pathname is undefined.'));
        }

        /* $var $template Magento_CustomAttribute_Block_Form_Template */
        $template = $this->getLayout()->getBlock($this->_xmlBlockName);
        if ($template && $template->getRenderers()) {
            foreach ($template->getRenderers() as $type => $data) {
                $this->addRenderer($type, $data['block'], $data['template']);
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Return attribute renderer by frontend input type
     *
     * @param string $type
     * @return Magento_CustomAttribute_Block_Form_Renderer_Abstract
     */
    public function getRenderer($type)
    {
        if (!isset($this->_renderBlocks[$type])) {
            if (isset($this->_renderBlockTypes[$type])) {
                $data   = $this->_renderBlockTypes[$type];
                $block  = $this->getLayout()->createBlock($data['block']);
                if ($block) {
                    $block->setTemplate($data['template']);
                }
            } else {
                $block = false;
            }
            $this->_renderBlocks[$type] = $block;
        }
        return $this->_renderBlocks[$type];
    }

    /**
     * Set Entity object
     *
     * @param Magento_Core_Model_Abstract $entity
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setEntity(Magento_Core_Model_Abstract $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Set entity model class for new object
     *
     * @param string $model
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setEntityModelClass($model)
    {
        $this->_entityModelClass = $model;
        return $this;
    }

    /**
     * Set Entity type if entity model entity type is not defined or is different
     *
     * @param int|string|Magento_Eav_Model_Entity_Type $entityType
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $this->_eavConfig->getEntityType($entityType);
        return $this;
    }

    /**
     * Return Entity object
     *
     * @return Magento_Core_Model_Abstract
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            if ($this->_entityModelClass) {
                $this->_entity = Mage::getModel($this->_entityModelClass);
            }
        }
        return $this->_entity;
    }

    /**
     * Set EAV entity form instance
     *
     * @param Magento_Eav_Model_Form $form
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setForm(Magento_Eav_Model_Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Set EAV entity Form code
     *
     * @param string $code
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setFormCode($code)
    {
        $this->_formCode = $code;
        return $this;
    }

    /**
     * Return EAV entity Form instance
     *
     * @return Magento_Eav_Model_Form
     */
    public function getForm()
    {
        if (is_null($this->_form)) {
            $this->_form = Mage::getModel($this->_formModelPath)
                ->setFormCode($this->_formCode)
                ->setEntity($this->getEntity());
            if ($this->_entityType) {
                $this->_form->setEntityType($this->_entityType);
            }
            $this->_form->initDefaultValues();
        }
        return $this->_form;
    }

    /**
     * Check EAV entity form has User defined attributes
     *
     * @return boolean
     */
    public function hasUserDefinedAttributes()
    {
        return count($this->getUserDefinedAttributes()) > 0;
    }

    /**
     * Return array of user defined attributes
     *
     * @return array
     */
    public function getUserDefinedAttributes()
    {
        $attributes = array();
        foreach ($this->getForm()->getUserAttributes() as $attribute) {
            if ($this->getExcludeFileAttributes() && in_array($attribute->getFrontendInput(), array('image', 'file'))) {
                continue;
            }
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * Render attribute row and return HTML
     *
     * @param Magento_Eav_Model_Attribute $attribute
     * @return string
     */
    public function getAttributeHtml(Magento_Eav_Model_Attribute $attribute)
    {
        $type   = $attribute->getFrontendInput();
        $block  = $this->getRenderer($type);
        if ($block) {
            $block->setAttributeObject($attribute)
                ->setEntity($this->getEntity())
                ->setFieldIdFormat($this->_fieldIdFormat)
                ->setFieldNameFormat($this->_fieldNameFormat);
            return $block->toHtml();
        }
        return false;
    }

    /**
     * Set format for HTML elements id attribute
     *
     * @param string $format
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setFieldIdFormat($format)
    {
        $this->_fieldIdFormat = $format;
        return $this;
    }

    /**
     * Set format for HTML elements name attribute
     *
     * @param string $format
     * @return Magento_CustomAttribute_Block_Form
     */
    public function setFieldNameFormat($format)
    {
        $this->_fieldNameFormat = $format;
        return $this;
    }

    /**
     * Check is show HTML container
     *
     * @return boolean
     */
    public function isShowContainer()
    {
        if ($this->hasData('show_container')) {
            return $this->getData('show_container');
        }
        return true;
    }
}
