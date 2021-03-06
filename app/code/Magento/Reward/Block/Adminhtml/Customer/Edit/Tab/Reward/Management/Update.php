<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        array $data = []
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
        $fieldset = $form->addFieldset('update_fieldset', ['legend' => __('Update Reward Points Balance')]);

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store',
                'select',
                [
                    'name' => 'store_id',
                    'title' => __('Store'),
                    'label' => __('Store'),
                    'values' => $this->_getStoreValues(),
                    'data-form-part' => $this->getData('target_form')
                ]
            );
        }

        $fieldset->addField(
            'points_delta',
            'text',
            [
                'name' => 'points_delta',
                'title' => __('Update Points'),
                'label' => __('Update Points'),
                'note' => __('Enter a negative number to subtract from the balance.'),
                'data-form-part' => $this->getData('target_form')
            ]
        );

        $fieldset->addField(
            'comment',
            'text',
            [
                'name' => 'comment',
                'title' => __('Comment'),
                'label' => __('Comment'),
                'data-form-part' => $this->getData('target_form')
            ]
        );

        $fieldset = $form->addFieldset('notification_fieldset', ['legend' => __('Reward Points Notifications')]);

        $fieldset->addField(
            'update_notification',
            'checkbox',
            [
                'name' => 'reward_update_notification',
                'label' => __('Subscribe for balance updates'),
                'checked' => (bool)$this->getCustomer()->getRewardUpdateNotification(),
                'value' => 1,
                'data-form-part' => $this->getData('target_form')
            ]
        );

        $fieldset->addField(
            'warning_notification',
            'checkbox',
            [
                'name' => 'reward_warning_notification',
                'label' => __('Subscribe for points expiration notifications'),
                'checked' => (bool)$this->getCustomer()->getRewardWarningNotification(),
                'value' => 1,
                'data-form-part' => $this->getData('target_form')
            ]
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
            [],
            [],
            [$customer->getWebsiteId()]
        );
        $values = [];

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach ($stores as $websiteId => $website) {
            $values[] = ['label' => $website['label'], 'value' => []];
            if (isset($website['children']) && is_array($website['children'])) {
                foreach ($website['children'] as $groupId => $group) {
                    if (isset($group['children']) && is_array($group['children'])) {
                        $options = [];
                        foreach ($group['children'] as $storeId => $store) {
                            $options[] = [
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                                'value' => $store['value'],
                            ];
                        }
                        $values[] = [
                            'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],
                            'value' => $options,
                        ];
                    }
                }
            }
        }
        return $values;
    }
}
