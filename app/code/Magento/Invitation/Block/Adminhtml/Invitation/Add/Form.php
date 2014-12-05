<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Block\Adminhtml\Invitation\Add;

use Magento\Customer\Api\GroupManagementInterface as CustomerGroupManagement;

/**
 * Invitation create form
 *
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Magento Store
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_store;

    /**
     * @var CustomerGroupManagement
     */
    protected $customerGroupManagement;

    /**
     * @var \Magento\Framework\Convert\Object
     */
    protected $_objectConverter;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $store
     * @param CustomerGroupManagement $customerGroupManagement
     * @param \Magento\Framework\Convert\Object $objectConverter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $store,
        CustomerGroupManagement $customerGroupManagement,
        \Magento\Framework\Convert\Object $objectConverter,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_store = $store;
        $this->customerGroupManagement = $customerGroupManagement;
        $this->_objectConverter = $objectConverter;
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
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            array('data' => array('id' => 'edit_form', 'action' => $this->getActionUrl(), 'method' => 'post'))
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('Invitations Information'), 'class' => 'fieldset-wide')
        );

        $fieldset->addField(
            'email',
            'textarea',
            array(
                'label' => __('Enter Each Email on New Line'),
                'required' => true,
                'class' => 'validate-emails',
                'name' => 'email'
            )
        );

        $fieldset->addField('message', 'textarea', array('label' => __('Message'), 'name' => 'message'));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'select',
                array(
                    'label' => __('Send From'),
                    'required' => true,
                    'name' => 'store_id',
                    'values' => $this->_store->getStoreValuesForForm()
                )
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        }

        $groups = $this->_objectConverter->toOptionHash(
            $this->customerGroupManagement->getLoggedInGroups(),
            'id',
            'code'
        );

        $fieldset->addField(
            'group_id',
            'select',
            array('label' => __('Invitee Group'), 'required' => true, 'name' => 'group_id', 'values' => $groups)
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($this->_backendSession->getInvitationFormData());

        return parent::_prepareForm();
    }
}
