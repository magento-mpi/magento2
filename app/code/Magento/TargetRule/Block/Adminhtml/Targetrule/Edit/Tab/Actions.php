<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab;

/**
 * TargetRule Adminhtml Edit Tab Actions Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
class Actions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_fieldset;

    /**
     * @var \Magento\TargetRule\Block\Adminhtml\Actions\Conditions
     */
    protected $_conditions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\TargetRule\Block\Adminhtml\Actions\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldset
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\TargetRule\Block\Adminhtml\Actions\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldset,
        array $data = array()
    ) {
        $this->_conditions = $conditions;
        $this->_fieldset = $fieldset;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare target rule actions form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\TargetRule\Model\Rule */
        $model = $this->_coreRegistry->registry('current_target_rule');
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset(
            'actions_fieldset',
            array('legend' => __('Product Result Conditions (leave blank for matching all products)'))
        );
        $newCondUrl = $this->getUrl('adminhtml/targetrule/newActionsHtml/', array('form' => $fieldset->getHtmlId()));
        $renderer = $this->_fieldset->setTemplate(
            'Magento_TargetRule::edit/conditions/fieldset.phtml'
        )->setNewChildUrl(
            $newCondUrl
        );
        $fieldset->setRenderer($renderer);

        $element = $fieldset->addField('actions', 'text', array('name' => 'actions', 'required' => true));
        $element->setRule($model);
        $element->setRenderer($this->_conditions);

        $model->getActions()->setJsFormObject($fieldset->getHtmlId());
        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Products to Display');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Products to Display');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
