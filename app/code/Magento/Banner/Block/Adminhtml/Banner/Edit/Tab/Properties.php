<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab;

/**
 * Main banner properties edit form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Properties extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Banner config
     *
     * @var \Magento\Banner\Model\Config
     */
    protected $_bannerConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Banner\Model\Config $bannerConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Banner\Model\Config $bannerConfig,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_bannerConfig = $bannerConfig;
    }

    /**
     * Set form id prefix, declare fields for banner properties
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'banner_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = $this->_coreRegistry->registry('current_banner');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Banner Properties')));

        if ($model->getBannerId()) {
            $fieldset->addField('banner_id', 'hidden', array('name' => 'banner_id'));
        }

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => __('Banner Name'),
                'name' => 'name',
                'required' => true,
                'disabled' => (bool)$model->getIsReadonly()
            )
        );

        $fieldset->addField(
            'is_enabled',
            'select',
            array(
                'label' => __('Active'),
                'name' => 'is_enabled',
                'required' => true,
                'disabled' => (bool)$model->getIsReadonly(),
                'options' => array(
                    \Magento\Banner\Model\Banner::STATUS_ENABLED => __('Yes'),
                    \Magento\Banner\Model\Banner::STATUS_DISABLED => __('No')
                )
            )
        );
        if (!$model->getId()) {
            $model->setData('is_enabled', \Magento\Banner\Model\Banner::STATUS_ENABLED);
        }

        // whether to specify banner types - for UI design purposes only
        $fieldset->addField(
            'is_types',
            'select',
            array(
                'label' => __('Applies To'),
                'options' => array('0' => __('Any Banner Type'), '1' => __('Specified Banner Types')),
                'disabled' => (bool)$model->getIsReadonly()
            )
        );
        $model->setIsTypes((string)(int)$model->getTypes());
        // see $form->setValues() below

        $fieldset->addField(
            'types',
            'multiselect',
            array(
                'label' => __('Specify Types'),
                'name' => 'types',
                'disabled' => (bool)$model->getIsReadonly(),
                'values' => $this->_bannerConfig->toOptionArray(false, false),
                'can_be_empty' => true
            )
        );

        $afterFormBlock = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        )->addFieldMap(
            "{$htmlIdPrefix}is_types",
            'is_types'
        )->addFieldMap(
            "{$htmlIdPrefix}types",
            'types'
        )->addFieldDependence(
            'types',
            'is_types',
            '1'
        );

        $this->_eventManager->dispatch(
            'banner_edit_tab_properties_after_prepare_form',
            array('model' => $model, 'form' => $form, 'block' => $this, 'after_form_block' => $afterFormBlock)
        );

        $this->setChild('form_after', $afterFormBlock);

        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
