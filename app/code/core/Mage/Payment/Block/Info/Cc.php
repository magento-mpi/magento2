<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Credit card generic payment info
 */
class Mage_Payment_Block_Info_Cc extends Mage_Payment_Block_Info
{
    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getCcTypeName()
    {
        $types = Mage::getSingleton('Mage_Payment_Model_Config')->getCcTypes();
        $ccType = $this->getInfo()->getCcType();
        if (isset($types[$ccType])) {
            return $types[$ccType];
        }
        return (empty($ccType)) ? Mage::helper('Mage_Payment_Helper_Data')->__('N/A') : $ccType;
    }

    /**
     * Whether current payment method has credit card expiration info
     *
     * @return bool
     */
    public function hasCcExpDate()
    {
        return (int)$this->getInfo()->getCcExpMonth() || (int)$this->getInfo()->getCcExpYear();
    }

    /**
     * Retrieve CC expiration month
     *
     * @return string
     */
    public function getCcExpMonth()
    {
        $month = $this->getInfo()->getCcExpMonth();
        if ($month<10) {
            $month = '0'.$month;
        }
        return $month;
    }

    /**
     * Retrieve CC expiration date
     *
     * @return Zend_Date
     */
    public function getCcExpDate()
    {
        $date = Mage::app()->getLocale()->date(0);
        $date->setYear($this->getInfo()->getCcExpYear());
        $date->setMonth($this->getInfo()->getCcExpMonth());
        return $date;
    }

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
        if ($ccType = $this->getCcTypeName()) {
            $data[Mage::helper('Mage_Payment_Helper_Data')->__('Credit Card Type')] = $ccType;
        }
        if ($this->getInfo()->getCcLast4()) {
            $data[Mage::helper('Mage_Payment_Helper_Data')->__('Credit Card Number')] = sprintf('xxxx-%s', $this->getInfo()->getCcLast4());
        }
        if (!$this->getIsSecureMode()) {
            if ($ccSsIssue = $this->getInfo()->getCcSsIssue()) {
                $data[Mage::helper('Mage_Payment_Helper_Data')->__('Switch/Solo/Maestro Issue Number')] = $ccSsIssue;
            }
            $year = $this->getInfo()->getCcSsStartYear();
            $month = $this->getInfo()->getCcSsStartMonth();
            if ($year && $month) {
                $data[Mage::helper('Mage_Payment_Helper_Data')->__('Switch/Solo/Maestro Start Date')] =  $this->_formatCardDate($year, $month);
            }
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }

    /**
     * Format year/month on the credit card
     *
     * @param string $year
     * @param string $month
     * @return string
     */
    protected function _formatCardDate($year, $month)
    {
        return sprintf('%s/%s', sprintf('%02d', $month), $year);
    }
}
