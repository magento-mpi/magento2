<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
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
            case Mage_Centinel_Model_Service::CMPI_PARES:
               return __('3D Secure Verification Result');
            case Mage_Centinel_Model_Service::CMPI_ENROLLED:
               return __('3D Secure Cardholder Validation');
            case Mage_Centinel_Model_Service::CMPI_ECI:
               return __('3D Secure Electronic Commerce Indicator');
            case Mage_Centinel_Model_Service::CMPI_CAVV:
               return __('3D Secure CAVV');
            case Mage_Centinel_Model_Service::CMPI_XID:
               return __('3D Secure XID');
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
            case Mage_Centinel_Model_Service::CMPI_PARES:
               return $this->_getCmpiParesValue($value);
            case Mage_Centinel_Model_Service::CMPI_ENROLLED:
               return $this->_getCmpiEnrolledValue($value);
            case Mage_Centinel_Model_Service::CMPI_ECI:
               return $this->_getCmpiEciValue($value);
            case Mage_Centinel_Model_Service::CMPI_CAVV: // break intentionally omitted
            case Mage_Centinel_Model_Service::CMPI_XID:
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
                return __('Merchant Liability');
            case '02':
            case '05':
            case '06':
                return __('Card Issuer Liability');
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
                return __('Enrolled');
            case 'U':
                return __('Enrolled but Authentication Unavailable');
            case 'N': // break intentionally omitted
            default:
                return __('Not Enrolled');
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
                return __('Successful');
            case 'N':
                return __('Failed');
            case 'U':
                return __('Unable to complete');
            case 'A':
                return __('Successful attempt');
            default:
                return $value;
        }
    }

    /**
     * Return centinel block for payment form with logos
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return Mage_Centinel_Block_Logo
     */
    public function getMethodFormBlock($method)
    {
        $blockType = 'Mage_Centinel_Block_Logo';
        $layout = $this->getLayout() ?: Mage::app()->getLayout();
        $block = $layout->createBlock($blockType);
        $block->setMethod($method);
        return $block;
    }

    /**
     * Return url of page about visa verification
     *
     * @return string
     */
    public function getVisaLearnMorePageUrl()
    {
        return 'https://usa.visa.com/personal/security/vbv/index.html?ep=v_sym_verifiedbyvisa';
    }

    /**
     * Return url of page about mastercard verification
     *
     * @return string
     */
    public function getMastercardLearnMorePageUrl()
    {
        return 'http://www.mastercardbusiness.com/mcbiz/index.jsp?template=/orphans&amp;content=securecodepopup';
    }
}
