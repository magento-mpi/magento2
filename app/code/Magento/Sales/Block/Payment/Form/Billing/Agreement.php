<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Billing Agreement form block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Payment_Form_Billing_Agreement extends Magento_Payment_Block_Form
{
    protected $_template = 'Magento_Sales::payment/form/billing/agreement.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setTransportName(Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract::TRANSPORT_BILLING_AGREEMENT_ID);
    }

    /**
     * Retrieve available customer billing agreements
     *
     * @return array
     */
    public function getBillingAgreements()
    {
        $data = array();
        $quote = $this->getParentBlock()->getQuote();
        if (!$quote || !$quote->getCustomer()) {
            return $data;
        }
        $collection = Mage::getModel('Magento_Sales_Model_Billing_Agreement')->getAvailableCustomerBillingAgreements(
            $quote->getCustomer()->getId()
        );

        foreach ($collection as $item) {
            $data[$item->getId()] = $item->getReferenceId();
        }
        return $data;
    }
}
