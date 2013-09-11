<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Region
 *
 * @method \Magento\Directory\Model\Resource\Region _getResource()
 * @method \Magento\Directory\Model\Resource\Region getResource()
 * @method string getCountryId()
 * @method \Magento\Directory\Model\Region setCountryId(string $value)
 * @method string getCode()
 * @method \Magento\Directory\Model\Region setCode(string $value)
 * @method string getDefaultName()
 * @method \Magento\Directory\Model\Region setDefaultName(string $value)
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class Region extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Directory\Model\Resource\Region');
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
