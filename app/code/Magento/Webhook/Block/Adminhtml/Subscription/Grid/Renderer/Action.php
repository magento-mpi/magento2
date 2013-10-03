<?php
/**
 * Renders html code for subscription grid items
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer;

class Action
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render a given html for the subscription grid
     *
     * @param \Magento\Object $row
     * @return string The rendered html code for a given row
     */
    public function render(\Magento\Object $row)
    {
        if (!($row instanceof \Magento\Webhook\Model\Subscription)) {
            return '';
        }

        switch ($row->getStatus()) {
            case \Magento\Webhook\Model\Subscription::STATUS_ACTIVE :
                return '<a href="' . $this->getUrl('*/webhook_subscription/revoke', array('id' => $row->getId()))
                    . '">' . __('Revoke') . '</a>';
            case \Magento\Webhook\Model\Subscription::STATUS_REVOKED :
                return '<a href="' . $this->getUrl('*/webhook_subscription/activate', array('id' => $row->getId()))
                    . '">' . __('Activate') . '</a>';
            case  \Magento\Webhook\Model\Subscription::STATUS_INACTIVE :
                $url = $this->getUrl('*/webhook_registration/activate', array('id' => $row->getId()));
                return '<a href="#" onclick="activateSubscription(\''. $url .'\'); return false;">'
                    . __('Activate') . '</a>';
            default :
                return '';
        }
    }
}
