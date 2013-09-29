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
namespace Magento\CustomAttribute\Block;

class Form extends \Magento\Core\Block\Template
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
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * EAV form instance
     *
     * @var \Magento\Eav\Model\Form
     */
    protected $_form;

    /**
     * EAV Entity Model
     *
     * @var \Magento\Core\Model\AbstractModel
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
     * @var \Magento\Core\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Eav\Model\Form\Factory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_modelFactory = $modelFactory;
        $this->_formFactory = $formFactory;
        $this->_eavConfig = $eavConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Add custom renderer block and template for rendering EAV entity attributes
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return \Magento\CustomAttribute\Block\Form
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
     * @return \Magento\CustomAttribute\Block\Form
     * @throws \Magento\Core\Exception
     */
    protected function _prepareLayout()
    {
        if (empty($this->_xmlBlockName)) {
            throw new \Magento\Core\Exception(__('The current module XML block name is undefined.'));
        }
        if (empty($this->_formModelPath)) {
            throw new \Magento\Core\Exception(__('The current module form model pathname is undefined.'));
        }

        /* $var $template \Magento\CustomAttribute\Block\Form\Template */
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
     * @return \Magento\CustomAttribute\Block\Form\Renderer\AbstractRenderer
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
     * @param \Magento\Core\Model\AbstractModel $entity
     * @return \Magento\CustomAttribute\Block\Form
     */
    public function setEntity(\Magento\Core\Model\AbstractModel $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Set entity model class for new object
     *
     * @param string $model
     * @return \Magento\CustomAttribute\Block\Form
     */
    public function setEntityModelClass($model)
    {
        $this->_entityModelClass = $model;
        return $this;
    }

    /**
     * Set Entity type if entity model entity type is not defined or is different
     *
     * @param int|string|\Magento\Eav\Model\Entity\Type $entityType
     * @return \Magento\CustomAttribute\Block\Form
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $this->_eavConfig->getEntityType($entityType);
        return $this;
    }

    /**
     * Return Entity object
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            if ($this->_entityModelClass) {
                $this->_entity = $this->_modelFactory->create($this->_entityModelClass);
            }
        }
        return $this->_entity;
    }

    /**
     * Set EAV entity form instance
     *
     * @param \Magento\Eav\Model\Form $form
     * @return \Magento\CustomAttribute\Block\Form
     */
    public function setForm(\Magento\Eav\Model\Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Set EAV entity Form code
     *
     * @param string $code
     * @return \Magento\CustomAttribute\Block\Form
     */
    public function setFormCode($code)
    {
        $this->_formCode = $code;
        return $this;
    }

    /**
     * Return EAV entity Form instance
     *
     * @return \Magento\Eav\Model\Form
     */
    public function getForm()
    {
        if (is_null($this->_form)) {
            $this->_form = $this->_formFactory->create($this->_formModelPath)
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
     * @param \Magento\Eav\Model\Attribute $attribute
     * @return string
     */
    public function getAttributeHtml(\Magento\Eav\Model\Attribute $attribute)
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
     * @return \Magento\CustomAttribute\Block\Form
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
     * @return \Magento\CustomAttribute\Block\Form
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
