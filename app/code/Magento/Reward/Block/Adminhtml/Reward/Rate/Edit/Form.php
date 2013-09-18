<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate edit form
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Reward_Rate_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Reward_Model_Source_WebsiteFactory
     */
    protected $_websitesFactory;

    /**
     * @var Magento_Reward_Model_Source_Customer_GroupsFactory
     */
    protected $_groupsFactory;

    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Reward_Model_Source_WebsiteFactory $websitesFactory,
        Magento_Reward_Model_Source_Customer_GroupsFactory $groupsFactory,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_websitesFactory = $websitesFactory;
        $this->_groupsFactory = $groupsFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Getter
     *
     * @return Magento_Reward_Model_Reward_Rate
     */
    public function getRate()
    {
        return $this->_coreRegistry->registry('current_reward_rate');
    }

    /**
     * Prepare form
     *
     * @return Magento_Reward_Block_Adminhtml_Reward_Rate_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('_current' => true)),
                'method' => 'post',
            ))
        );
        $form->setFieldNameSuffix('rate');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Reward Exchange Rate Information')
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'   => 'website_id',
                'title'  => __('Website'),
                'label'  => __('Website'),
                'values' => $this->_websitesFactory->create()->toOptionArray(),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('customer_group_id', 'select', array(
            'name'   => 'customer_group_id',
            'title'  => __('Customer Group'),
            'label'  => __('Customer Group'),
            'values' => $this->_groupsFactory->create()->toOptionArray()
        ));

        $fieldset->addField('direction', 'select', array(
            'name'   => 'direction',
            'title'  => __('Direction'),
            'label'  => __('Direction'),
            'values' => $this->getRate()->getDirectionsOptionArray()
        ));

        $rateRenderer = $this->getLayout()
            ->createBlock('Magento_Reward_Block_Adminhtml_Reward_Rate_Edit_Form_Renderer_Rate')
            ->setRate($this->getRate());
        $direction = $this->getRate()->getDirection();
        if ($direction == Magento_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY) {
            $fromIndex = 'points';
            $toIndex = 'currency_amount';
        } else {
            $fromIndex = 'currency_amount';
            $toIndex = 'points';
        }
        $fieldset->addField('rate_to_currency', 'note', array(
            'title'             => __('Rate'),
            'label'             => __('Rate'),
            'value_index'       => $fromIndex,
            'equal_value_index' => $toIndex
        ))->setRenderer($rateRenderer);

        $form->setUseContainer(true);
        $form->setValues($this->getRate()->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
