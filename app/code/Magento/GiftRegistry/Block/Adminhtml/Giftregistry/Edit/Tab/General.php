<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab;

class General
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $sourceYesNo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Backend\Model\Config\Source\Yesno $sourceYesNo
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Config\Source\Yesno $sourceYesNo,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->sourceYesNo = $sourceYesNo;
    }

    /**
     * Return current gift registry type instance
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function getType()
    {
        return $this->_coreRegistry->registry('current_giftregistry_type');
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getLayout()->hasElement($this->getNameInLayout() . '_element')) {
            $this->getLayout()->unsetElement($this->getNameInLayout() . '_element');
        }
        \Magento\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
    }

    /**
     * Prepare general properties form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('type');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'  => __('General Information')
        ));

        if ($this->getType()->getId()) {
            $fieldset->addField('type_id', 'hidden', array(
                'name' => 'type_id'
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => __('Code'),
            'required' => true,
            'class'    => 'validate-code'
        ));

        $fieldset->addField('label', 'text', array(
            'name'     => 'label',
            'label'    => __('Label'),
            'required' => true,
            'scope'    => 'store'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'     => 'sort_order',
            'label'    => __('Sort Order'),
            'scope'    => 'store'
        ));

        $fieldset->addField('is_listed', 'select', array(
            'label'    => __('Is Listed'),
            'name'     => 'is_listed',
            'values'   => $this->sourceYesNo->toOptionArray(),
            'scope'    => 'store'
        ));

        $form->setValues($this->getType()->getData());
        $this->setForm($form);
        $form->setDataObject($this->getType());

        return parent::_prepareForm();
    }
}
