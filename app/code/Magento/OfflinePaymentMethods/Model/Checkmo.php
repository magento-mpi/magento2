<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\OfflinePaymentMethods\Model;

class Checkmo extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code  = 'checkmo';
    protected $_formBlockType = 'Magento\OfflinePaymentMethods\Block\Form\Checkmo';
    protected $_infoBlockType = 'Magento\OfflinePaymentMethods\Block\Info\Checkmo';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  \Magento\OfflinePaymentMethods\Model\Checkmo
     */
    public function assignData($data)
    {
        $details = array();
        if ($this->getPayableTo()) {
            $details['payable_to'] = $this->getPayableTo();
        }
        if ($this->getMailingAddress()) {
            $details['mailing_address'] = $this->getMailingAddress();
        }
        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
        return $this;
    }

    public function getPayableTo()
    {
        return $this->getConfigData('payable_to');
    }

    public function getMailingAddress()
    {
        return $this->getConfigData('mailing_address');
    }

}
