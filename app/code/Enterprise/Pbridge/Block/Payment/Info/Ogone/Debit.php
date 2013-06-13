<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Info payment block for Ogone Direct Debit
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Payment_Info_Ogone_Debit extends Mage_Payment_Block_Info
{
    /**
     * Prepare credit card related payment info
     *
     * @param Varien_Object|array $transport
     * @return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);

        $data = array();

        $details = @unserialize($this->getInfo()->getAdditionalData());
        if (!isset($details['pbridge_data']['x_params'])) {
            return $transport;
        }

        $xParams = @unserialize($details['pbridge_data']['x_params']);

        if (isset($xParams['dd_bankaccountholder']) && !empty($xParams['dd_bankaccountholder'])) {
            $data[Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Account holder')] = $xParams['dd_bankaccountholder'];
        }

        if (isset($xParams['dd_bankaccount'])) {
            $data[Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Account number')] = sprintf('xxxx-%s', $xParams['dd_bankaccount']);
        }

        if (isset($xParams['dd_bankcode']) && !empty($xParams['dd_bankcode'])) {
            $data[Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Bank code')] = $xParams['dd_bankcode'];
        }

        if (!empty($data)) {
            return $transport->setData(array_merge($data, $transport->getData()));
        } else {
            return $transport;
        }
    }
}
