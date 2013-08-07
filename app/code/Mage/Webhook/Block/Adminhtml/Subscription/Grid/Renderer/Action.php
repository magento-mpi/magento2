<?php
/**
 * Renders html code for subscription grid items
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render a given html for the subscription grid
     *
     * @param Varien_Object $row
     * @return string The rendered html code for a given row
     */
    public function render(Varien_Object $row)
    {
        if (!($row instanceof Mage_Webhook_Model_Subscription)) {
            return '';
        }

        switch ($row->getStatus()) {
            case Mage_Webhook_Model_Subscription::STATUS_ACTIVE :
                return '<a href="' . $this->getUrl('*/webhook_subscription/revoke', array('id' => $row->getId()))
                    . '">' . $this->__('Revoke') . '</a>';
            case Mage_Webhook_Model_Subscription::STATUS_REVOKED :
                return '<a href="' . $this->getUrl('*/webhook_subscription/activate', array('id' => $row->getId()))
                    . '">' . $this->__('Activate') . '</a>';
            case  Mage_Webhook_Model_Subscription::STATUS_INACTIVE :
                $url = $this->getUrl('*/webhook_registration/activate', array('id' => $row->getId()));
                return '<a href="#" onclick="activateSubscription(\''. $url .'\'); return false;">'
                    . $this->__('Activate') . '</a>';
            default :
                return '';
        }
    }
}
