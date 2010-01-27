<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Centinel
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Centinel module base helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return label for cmpi field 
     *
     * @param string $fieldName
     * @return string
     */
    public function getCmpiLabel($fieldName)
    {
        switch ($fieldName) {
            case Mage_Centinel_Model_Service::CMPI_FIELD_PARES:
               return $this->__('Centinel Transaction Status');
            case Mage_Centinel_Model_Service::CMPI_FIELD_ENROLLED:
               return $this->__('Centinel Status of Availability');
            case Mage_Centinel_Model_Service::CMPI_FIELD_CAVV:
               return $this->__('Centinel Cardholder Authentification Verification Value');
            case Mage_Centinel_Model_Service::CMPI_FIELD_ECI:
               return $this->__('Centinel Electronic Commerce Indicator');
            case Mage_Centinel_Model_Service::CMPI_FIELD_XID:
               return $this->__('Centinel Transaction Xid');
        }
        return '';
    }

    /**
     * Return value for cmpi field 
     *
     * @param string $fieldName
     * @param string $value
     * @return string
     */
    public function getCmpiValue($fieldName, $value)
    {
        switch ($fieldName) {
            case Mage_Centinel_Model_Service::CMPI_FIELD_PARES:
               return $this->_getCmpiParesValue($value);
            case Mage_Centinel_Model_Service::CMPI_FIELD_ENROLLED:
               return $this->_getCmpiEnrolledValue($value);
            case Mage_Centinel_Model_Service::CMPI_FIELD_CAVV:
               return $value;
            case Mage_Centinel_Model_Service::CMPI_FIELD_ECI:
               return $this->_getCmpiEciValue($value);
            case Mage_Centinel_Model_Service::CMPI_FIELD_XID:
               return $value;
        }
        return '';
    }

    /**
     * Return text value for cmpi eci flag field
     *
     * @param string $value
     * @return string
     */
    private function _getCmpiEciValue($value)
    {
        switch ($value) {
            case '01':
            case '07':
                return $this->__('Indicates Merchant Liability');
            case '02':
            case '05':
            case '06':
                return $this->__('Indicates Card Issuer Liability');
            default:
                return $value;
        }
    }

    /**
     * Return text value for cmpi enrolled field
     *
     * @param string $value
     * @return string
     */
    private function _getCmpiEnrolledValue($value)
    {
        switch ($value) {
            case 'Y':
                return $this->__('Cardholder Enrolled');
            case 'N':
                return $this->__('Not Enrolled');
            case 'U':
                return $this->__('Cardholder Enrolled but Authentication Unavailable');
            default:
                return $value;
        }
    }

    /**
     * Return text value for cmpi pares field
     *
     * @param string $value
     * @return string
     */
    private function _getCmpiParesValue($value)
    {
        switch ($value) {
            case 'Y':
                return $this->__('Success Transaction');
            case 'N':
                return $this->__('Failed Transaction');
            case 'U':
                return $this->__('Unable to Complete Transaction');
            case 'A':
                return $this->__('Successful Attempts Transaction');
            default:
                return $value;
        }
    }
}

