<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Block\Payment\Info\Payone;

class Debit extends \Magento\Payment\Block\Info
{
    /**
     * Prepare credit card related payment info
     *
     * @param \Magento\Framework\Object|array $transport
     * @return \Magento\Framework\Object|array
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);

        $data = [];

        $details = @unserialize($this->getInfo()->getAdditionalData());
        if (!isset($details['pbridge_data']['x_params'])) {
            return $transport;
        }

        $xParams = @unserialize($details['pbridge_data']['x_params']);

        if (isset($xParams['dd_bankaccountholder'])) {
            $data[__('Account holder')] = $xParams['dd_bankaccountholder'];
        }

        if (isset($xParams['dd_bankaccount'])) {
            $data[__('Account number')] = sprintf('xxxx-%s', $xParams['dd_bankaccount']);
        }

        if (isset($xParams['dd_bankcode'])) {
            $data[__('Bank code')] = $xParams['dd_bankcode'];
        }

        if (!empty($data)) {
            return $transport->setData(array_merge($data, $transport->getData()));
        } else {
            return $transport;
        }
    }
}
