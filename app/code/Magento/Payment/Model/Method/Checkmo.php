<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Payment_Model_Method_Checkmo extends Magento_Payment_Model_Method_Abstract
{
    protected $_code  = 'checkmo';
    protected $_formBlockType = 'Magento_Payment_Block_Form_Checkmo';
    protected $_infoBlockType = 'Magento_Payment_Block_Info_Checkmo';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Magento_Payment_Model_Method_Checkmo
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
