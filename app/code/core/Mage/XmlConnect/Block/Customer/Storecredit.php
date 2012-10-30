<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store Credits xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Storecredit extends Mage_Core_Block_Template
{
    /**
     * Render customer store credits xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
        $xmlModel = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<store_credits_info></store_credits_info>'));

        $accountBalance = $this->getLayout()
            ->addBlock('Enterprise_CustomerBalance_Block_Account_Balance', 'account_balance');

        $xmlModel->addCustomChild('balance', null, array(
            'label' => $this->__('Your current balance is:'),
            'value' => $accountBalance->getBalance(),
            'formatted_value' => Mage::helper('Mage_Core_Helper_Data')->currency($accountBalance->getBalance(), true, false)
        ));

        $accountHistory = $this->getLayout()
            ->addBlock('Enterprise_CustomerBalance_Block_Account_History', 'account_history');

        if ($accountHistory->canShow() && $accountHistory->getEvents() && count($accountHistory->getEvents())) {
            $balanceHistory = $xmlModel->addCustomChild('balance_history', null, array(
                'label' => $this->__('Balance History'),
                'action_label' => $this->__('Action'),
                'balance_change_label' => $this->__('Balance Change'),
                'balance_label' => $this->__('Balance'),
                'date_label' => $this->__('Date')
            ));

            foreach ($accountHistory->getEvents() as $event) {
                $item = $balanceHistory->addCustomChild('item');
                $item->addCustomChild('action', null, array(
                    'value' => $accountHistory->getActionLabel($event->getAction())
                ));

                $item->addCustomChild('balance_change', null, array(
                    'value' => Mage::helper('Mage_Core_Helper_Data')->currency($event->getBalanceDelta(), true, false)
                ));

                $item->addCustomChild('balance', null, array(
                    'value' => Mage::helper('Mage_Core_Helper_Data')->currency($event->getBalanceAmount(), true, false)
                ));

                $item->addCustomChild('date', null, array(
                    'value' => Mage::helper('Mage_Core_Helper_Data')->formatDate($event->getUpdatedAt(), 'short', true)
                ));
            }
        }

        return $xmlModel->asNiceXml();
    }
}