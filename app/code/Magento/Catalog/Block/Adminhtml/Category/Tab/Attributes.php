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
 * Adminhtml Catalog Category Attributes per Group Tab block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Category\Tab;

class Attributes extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve Category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Initialize tab
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_wysiwygConfig->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes
     */
    protected function _prepareForm()
    {
        $group      = $this->getGroup();
        $attributes = $this->getAttributes();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('group_' . $group->getId());
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('fieldset_group_' . $group->getId(), array(
            'legend'    => __($group->getAttributeGroupName()),
            'class'     => 'fieldset-wide',
        ));

        if ($this->getAddHiddenFields()) {
            if (!$this->getCategory()->getId()) {
                // path
                if ($this->getRequest()->getParam('parent')) {
                    $fieldset->addField('path', 'hidden', array(
                        'name'  => 'path',
                        'value' => $this->getRequest()->getParam('parent')
                    ));
                } else {
                    $fieldset->addField('path', 'hidden', array(
                        'name'  => 'path',
                        'value' => 1
                    ));
                }
            } else {
                $fieldset->addField('id', 'hidden', array(
                    'name'  => 'id',
                    'value' => $this->getCategory()->getId()
                ));
                $fieldset->addField('path', 'hidden', array(
                    'name'  => 'path',
                    'value' => $this->getCategory()->getPath()
                ));
            }
        }

        $this->_setFieldset($attributes, $fieldset);

        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            if ($attribute->getAttributeCode() == 'url_key') {
                if ($this->getCategory()->getLevel() == 1) {
                    $fieldset->removeField('url_key');
                    $fieldset->addField('url_key', 'hidden', array(
                        'name'  => 'url_key',
                        'value' => $this->getCategory()->getUrlKey()
                    ));
                } else {
                    $form->getElement('url_key')->setRenderer(
                        $this->getLayout()
                            ->createBlock('Magento\Catalog\Block\Adminhtml\Form\Renderer\Attribute\Urlkey')
                    );
                }
            }
        }

        if ($this->getCategory()->getLevel() == 1) {
            $fieldset->removeField('custom_use_parent_settings');
        } else {
            if ($this->getCategory()->getCustomUseParentSettings()) {
                foreach ($this->getCategory()->getDesignAttributes() as $attribute) {
                    if ($element = $form->getElement($attribute->getAttributeCode())) {
                        $element->setDisabled(true);
                    }
                }
            }
            if ($element = $form->getElement('custom_use_parent_settings')) {
                $element->setData('onchange', 'onCustomUseParentChanged(this)');
            }
        }

        if ($this->getCategory()->hasLockedAttributes()) {
            foreach ($this->getCategory()->getLockedAttributes() as $attribute) {
                if ($element = $form->getElement($attribute)) {
                    $element->setReadonly(true, true);
                }
            }
        }

        if (!$this->getCategory()->getId()) {
            $this->getCategory()->setIncludeInMenu(1);
        }

        $form->addValues($this->getCategory()->getData());

        $this->_eventManager->dispatch('adminhtml_catalog_category_edit_prepare_form', array('form'=>$form));

        $form->setFieldNameSuffix('general');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => 'Magento\Catalog\Block\Adminhtml\Category\Helper\Image',
            'textarea' => 'Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg'
        );
    }
}
