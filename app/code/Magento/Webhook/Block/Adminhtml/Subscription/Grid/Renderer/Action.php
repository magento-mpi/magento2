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
class Magento_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render a given html for the subscription grid
     *
     * @param Magento_Object $row
     * @return string The rendered html code for a given row
     */
    public function render(Magento_Object $row)
    {
        if (!($row instanceof Magento_Webhook_Model_Subscription)) {
            return '';
        }

        switch ($row->getStatus()) {
            case Magento_Webhook_Model_Subscription::STATUS_ACTIVE :
                return '<a href="' . $this->getUrl('*/webhook_subscription/revoke', array('id' => $row->getId()))
                    . '">' . __('Revoke') . '</a>';
            case Magento_Webhook_Model_Subscription::STATUS_REVOKED :
                return '<a href="' . $this->getUrl('*/webhook_subscription/activate', array('id' => $row->getId()))
                    . '">' . __('Activate') . '</a>';
            case  Magento_Webhook_Model_Subscription::STATUS_INACTIVE :
                $url = $this->getUrl('*/webhook_registration/activate', array('id' => $row->getId()));
                return '<a href="#" onclick="activateSubscription(\''. $url .'\'); return false;">'
                    . __('Activate') . '</a>';
            default :
                return '';
        }
    }
}
