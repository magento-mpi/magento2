<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Design\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\View\Design\Theme\LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\View\Design\Theme\LabelFactory $labelFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\View\Design\Theme\LabelFactory $labelFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_labelFactory = $labelFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('general', array(
            'legend' => __('General Settings'))
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store'),
                'title'    => __('Store'),
                'values'   => $this->_systemStore->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true,
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => $this->_storeManager->getStore(true)->getId(),
            ));
        }

        /** @var $label \Magento\View\Design\Theme\Label */
        $label = $this->_labelFactory->create();
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $fieldset->addField('design', 'select', array(
            'label'    => __('Custom Design'),
            'title'    => __('Custom Design'),
            'values'   => $options,
            'name'     => 'design',
            'required' => true,
        ));

        $dateFormat = $this->_localeDate->getDateFormat(\Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('date_from', 'date', array(
            'label'    => __('Date From'),
            'title'    => __('Date From'),
            'name'     => 'date_from',
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            //'required' => true,
        ));
        $fieldset->addField('date_to', 'date', array(
            'label'    => __('Date To'),
            'title'    => __('Date To'),
            'name'     => 'date_to',
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            //'required' => true,
        ));

        $formData = $this->_backendSession->getDesignData(true);
        if (!$formData) {
            $formData = $this->_coreRegistry->registry('design')->getData();
        } else {
            $formData = $formData['design'];
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
    }

}
