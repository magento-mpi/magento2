<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Region
 *
 * @method Mage_Directory_Model_Resource_Region _getResource()
 * @method Mage_Directory_Model_Resource_Region getResource()
 * @method string getCountryId()
 * @method Mage_Directory_Model_Region setCountryId(string $value)
 * @method string getCode()
 * @method Mage_Directory_Model_Region setCode(string $value)
 * @method string getDefaultName()
 * @method Mage_Directory_Model_Region setDefaultName(string $value)
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Region extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Directory_Model_Resource_Region');
    }

    /**
     * Retrieve region name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getData('name');
        if (is_null($name)) {
            $name = $this->getData('default_name');
        }
        return $name;
    }

    public function loadByCode($code, $countryId)
    {
        if ($code) {
            $this->_getResource()->loadByCode($this, $code, $countryId);
        }
        return $this;
    }

    public function loadByName($name, $countryId)
    {
        $this->_getResource()->loadByName($this, $name, $countryId);
        return $this;
    }

}
