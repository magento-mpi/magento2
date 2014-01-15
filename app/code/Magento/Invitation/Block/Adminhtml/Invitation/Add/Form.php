<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invintation create form
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation\Add;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Magento Store
     *
     * @var \Magento\Core\Model\System\Store
     */
    protected $_store;

    /**
     * Customer Group Factory
     *
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_groupFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\System\Store $store
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Model\System\Store $store,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_store = $store;
        $this->_groupFactory = $groupFactory;
    }

    /**
     * Return invitation form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('invitations/*/save', array('_current' => true));
    }

    /**
     * Prepare invitation form
     *
     * @return \Magento\Invitation\Block\Adminhtml\Invitation\Add\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'data' => array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Invitations Information'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('email', 'textarea', array(
            'label' => __('Enter Each Email on New Line'),
            'required' => true,
            'class' => 'validate-emails',
            'name' => 'email'
        ));

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name' => 'message'
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label' => __('Send From'),
                'required' => true,
                'name' => 'store_id',
                'values' => $this->_store->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $groups = $this->_groupFactory->create()->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $fieldset->addField('group_id', 'select', array(
            'label' => __('Invitee Group'),
            'required' => true,
            'name' => 'group_id',
            'values' => $groups
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($this->_backendSession->getInvitationFormData());

        return parent::_prepareForm();
    }

}
