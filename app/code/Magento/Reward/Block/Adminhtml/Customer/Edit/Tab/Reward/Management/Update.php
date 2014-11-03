<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management;

/**
 * Reward update points form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Update extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Core system store model
     *
     * @var \Magento\Store\Model\System\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\StoreFactory $storeFactory,
        array $data = array()
    ) {
        $this->_storeFactory = $storeFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Getter
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_coreRegistry->registry('current_customer');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('reward_');
        $form->setFieldNameSuffix('reward');
        $fieldset = $form->addFieldset('update_fieldset', array('legend' => __('Update Reward Points Balance')));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store',
                'select',
                array(
                    'name' => 'store_id',
                    'title' => __('Store'),
                    'label' => __('Store'),
                    'values' => $this->_getStoreValues(),
                    'data-form-part' => $this->getData('target_form')
                )
            );
        }

        $fieldset->addField(
            'points_delta',
            'text',
            array(
                'name' => 'points_delta',
                'title' => __('Update Points'),
                'label' => __('Update Points'),
                'note' => __('Enter a negative number to subtract from the balance.'),
                'data-form-part' => $this->getData('target_form')
            )
        );

        $fieldset->addField(
            'comment',
            'text',
            array(
                'name' => 'comment',
                'title' => __('Comment'),
                'label' => __('Comment'),
                'data-form-part' => $this->getData('target_form')
            )
        );

        $fieldset = $form->addFieldset('notification_fieldset', array('legend' => __('Reward Points Notifications')));

        $fieldset->addField(
            'update_notification',
            'checkbox',
            array(
                'name' => 'reward_update_notification',
                'label' => __('Subscribe for balance updates'),
                'checked' => (bool)$this->getCustomer()->getRewardUpdateNotification(),
                'value' => 1,
                'data-form-part' => $this->getData('target_form')
            )
        );

        $fieldset->addField(
            'warning_notification',
            'checkbox',
            array(
                'name' => 'reward_warning_notification',
                'label' => __('Subscribe for points expiration notifications'),
                'checked' => (bool)$this->getCustomer()->getRewardWarningNotification(),
                'value' => 1,
                'data-form-part' => $this->getData('target_form')
            )
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Retrieve source values for store drop-dawn
     *
     * @return array
     */
    protected function _getStoreValues()
    {
        $customer = $this->getCustomer();
        if (!$customer->getWebsiteId() ||
            $this->_storeManager->hasSingleStore() ||
            $customer->getSharingConfig()->isGlobalScope()
        ) {
            return $this->_storeFactory->create()->getStoreValuesForForm();
        }

        $stores = $this->_storeFactory->create()->getStoresStructure(
            false,
            array(),
            array(),
            array($customer->getWebsiteId())
        );
        $values = array();

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach ($stores as $websiteId => $website) {
            $values[] = array('label' => $website['label'], 'value' => array());
            if (isset($website['children']) && is_array($website['children'])) {
                foreach ($website['children'] as $groupId => $group) {
                    if (isset($group['children']) && is_array($group['children'])) {
                        $options = array();
                        foreach ($group['children'] as $storeId => $store) {
                            $options[] = array(
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                                'value' => $store['value']
                            );
                        }
                        $values[] = array(
                            'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],
                            'value' => $options
                        );
                    }
                }
            }
        }
        return $values;
    }
}
