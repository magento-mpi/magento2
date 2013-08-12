<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Magento_Sales_Model_Order_Address for address variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
abstract class Saas_PrintedTemplate_Model_Variable_Address_Abstract
    extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Cache for getCountry()
     *
     * @var string
     */
    protected $_countryName;

    /**
     * Returns country name or ID if cannot find it
     *
     * @return string
     */
    public function getCountry()
    {
        if (!$this->_countryName) {
            $countryId = $this->_value->getCountryId();
            $country = $this->_getCountryModel()->load($countryId);
            $this->_countryName = ($country->getId()) ? $country->getName() : $countryId;
        }

        return $this->_countryName;
    }

    /**
     * Get Country
     */
    protected function _getCountryModel()
    {
        return Mage::getModel('Mage_Directory_Model_Country');
    }

    /**
     * Return streets concatenated with semicolon
     *
     * @return string Concatenated with semicolon
     */
    public function getStreet()
    {
        return join('; ', $this->_value->getStreet());
    }

    /**
     * Return streets concatenated with <br/> tag
     *
     * @return string Concatenated with <br/> addresses
     */
    public function getStreetMultiline()
    {
        return join('<br/> ', $this->_value->getStreet());
    }
}
