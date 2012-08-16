<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Payment_Block_Info_Checkmo extends Mage_Payment_Block_Info
{

    protected $_payableTo;
    protected $_mailingAddress;

    protected $_template = 'Mage_Payment::info/checkmo.phtml';

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPayableTo()
    {
        if (is_null($this->_payableTo)) {
            $this->_convertAdditionalData();
        }
        return $this->_payableTo;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getMailingAddress()
    {
        if (is_null($this->_mailingAddress)) {
            $this->_convertAdditionalData();
        }
        return $this->_mailingAddress;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Payment_Block_Info_Checkmo
     */
    protected function _convertAdditionalData()
    {
        $details = @unserialize($this->getInfo()->getAdditionalData());
        if (is_array($details)) {
            $this->_payableTo = isset($details['payable_to']) ? (string) $details['payable_to'] : '';
            $this->_mailingAddress = isset($details['mailing_address']) ? (string) $details['mailing_address'] : '';
        } else {
            $this->_payableTo = '';
            $this->_mailingAddress = '';
        }
        return $this;
    }

    public function toPdf()
    {
        $this->setTemplate('Mage_Payment::info/pdf/checkmo.phtml');
        return $this->toHtml();
    }

}
