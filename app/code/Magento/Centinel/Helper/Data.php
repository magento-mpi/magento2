<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel module base helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Centinel_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Layout factory
     *
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Layout $layoutFactory
     */
    public function __construct(Magento_Core_Helper_Context $context, Magento_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Return label for cmpi field
     *
     * @param string $fieldName
     * @return string
     */
    public function getCmpiLabel($fieldName)
    {
        switch ($fieldName) {
            case Magento_Centinel_Model_Service::CMPI_PARES:
               return __('3D Secure Verification Result');
            case Magento_Centinel_Model_Service::CMPI_ENROLLED:
               return __('3D Secure Cardholder Validation');
            case Magento_Centinel_Model_Service::CMPI_ECI:
               return __('3D Secure Electronic Commerce Indicator');
            case Magento_Centinel_Model_Service::CMPI_CAVV:
               return __('3D Secure CAVV');
            case Magento_Centinel_Model_Service::CMPI_XID:
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
            case Magento_Centinel_Model_Service::CMPI_PARES:
               return $this->_getCmpiParesValue($value);
            case Magento_Centinel_Model_Service::CMPI_ENROLLED:
               return $this->_getCmpiEnrolledValue($value);
            case Magento_Centinel_Model_Service::CMPI_ECI:
               return $this->_getCmpiEciValue($value);
            case Magento_Centinel_Model_Service::CMPI_CAVV: // break intentionally omitted
            case Magento_Centinel_Model_Service::CMPI_XID:
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
     * @param Magento_Payment_Model_Method_Abstract $method
     * @return Magento_Centinel_Block_Logo
     */
    public function getMethodFormBlock($method)
    {
        $block = $this->_layout->createBlock('Magento_Centinel_Block_Logo');
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
