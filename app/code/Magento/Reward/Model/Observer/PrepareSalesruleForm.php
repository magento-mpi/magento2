<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class PrepareSalesruleForm
{
    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData
    ) {
        $this->_rewardData = $rewardData;
    }

    /**
     * Prepare salesrule form. Add field to specify reward points delta
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('action_fieldset');
        $fieldset->addField(
            'reward_points_delta',
            'text',
            [
                'name' => 'reward_points_delta',
                'label' => __('Add Reward Points'),
                'title' => __('Add Reward Points')
            ],
            'stop_rules_processing'
        );
        return $this;
    }
}
