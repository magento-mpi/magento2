<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Core registry
     *
     * @var Magento_Backend_Block_Widget_Form_Renderer_Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var Magento_Rule_Block_Actions
     */
    protected $_ruleActions;

    /**
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $_sourceYesno;

    /**
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesno
     * @param Magento_Rule_Block_Actions $ruleActions
     * @param Magento_Backend_Block_Widget_Form_Renderer_Fieldset $rendererFieldset
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Config_Source_Yesno $sourceYesno,
        Magento_Rule_Block_Actions $ruleActions,
        Magento_Backend_Block_Widget_Form_Renderer_Fieldset $rendererFieldset,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_ruleActions = $ruleActions;
        $this->_sourceYesno = $sourceYesno;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_quote_rule');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend' => __('Update prices using the following information')
        ));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => __('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                Magento_SalesRule_Model_Rule::BY_PERCENT_ACTION => __('Percent of product price discount'),
                Magento_SalesRule_Model_Rule::BY_FIXED_ACTION => __('Fixed amount discount'),
                Magento_SalesRule_Model_Rule::CART_FIXED_ACTION => __('Fixed amount discount for whole cart'),
                Magento_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION => __('Buy X get Y free (discount amount is Y)'),
            ),
        ));
        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => __('Discount Amount'),
        ));
        $model->setDiscountAmount($model->getDiscountAmount()*1);

        $fieldset->addField('discount_qty', 'text', array(
            'name' => 'discount_qty',
            'label' => __('Maximum Qty Discount is Applied To'),
        ));
        $model->setDiscountQty($model->getDiscountQty()*1);

        $fieldset->addField('discount_step', 'text', array(
            'name' => 'discount_step',
            'label' => __('Discount Qty Step (Buy X)'),
        ));

        $fieldset->addField('apply_to_shipping', 'select', array(
            'label'     => __('Apply to Shipping Amount'),
            'title'     => __('Apply to Shipping Amount'),
            'name'      => 'apply_to_shipping',
            'values'    => $this->_sourceYesno->toOptionArray(),
        ));

        $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => __('Free Shipping'),
            'title'     => __('Free Shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => array(
                0 => __('No'),
                Magento_SalesRule_Model_Rule::FREE_SHIPPING_ITEM => __('For matching items only'),
                Magento_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => __('For shipment with matching items'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => __('Stop Further Rules Processing'),
            'title'     => __('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
        ));

        $renderer = $this->_rendererFieldset
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_quote/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>__('Apply the rule only to cart items matching the following conditions '
                . '(leave blank for all items).')
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => __('Apply To'),
            'title' => __('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer($this->_ruleActions);

        $this->_eventManager->dispatch('adminhtml_block_salesrule_actions_prepareform', array('form' => $form));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
