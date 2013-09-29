<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Adminhtml Edit Tab Conditions Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab;

class Conditions
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\TargetRule\Block\Adminhtml\Rule\Conditions
     */
    protected $_conditions;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_fieldset;

    /**
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldset
     * @param \Magento\TargetRule\Block\Adminhtml\Rule\Conditions $conditions
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldset,
        \Magento\TargetRule\Block\Adminhtml\Rule\Conditions $conditions,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_conditions = $conditions;
        $this->_fieldset = $fieldset;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }


    /**
     * Prepare target rule actions form before rendering HTML
     *
     * @return \Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Conditions
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\TargetRule\Model\Rule */
        $model  = $this->_coreRegistry->registry('current_target_rule');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset   = $form->addFieldset('conditions_fieldset', array(
            'legend' => __('Product Match Conditions (leave blank for matching all products)'))
        );
        $newCondUrl = $this->getUrl('*/targetrule/newConditionHtml/', array(
            'form'  => $fieldset->getHtmlId()
        ));
        $renderer   = $this->_fieldset->setTemplate('Magento_TargetRule::edit/conditions/fieldset.phtml')
            ->setNewChildUrl($newCondUrl);
        $fieldset->setRenderer($renderer);

        $element    = $fieldset->addField('conditions', 'text', array(
            'name'      => 'conditions',
            'required'  => true,
        ));

        $element->setRule($model);
        $element->setRenderer($this->_conditions);

        $model->getConditions()->setJsFormObject($fieldset->getHtmlId());
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
        return __('Products to Match');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Products to Match');
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
