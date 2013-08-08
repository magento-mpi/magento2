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
 * Directory country format model
 *
 * @method Mage_Directory_Model_Resource_Country_Format _getResource()
 * @method Mage_Directory_Model_Resource_Country_Format getResource()
 * @method string getCountryId()
 * @method Mage_Directory_Model_Country_Format setCountryId(string $value)
 * @method string getType()
 * @method Mage_Directory_Model_Country_Format setType(string $value)
 * @method string getFormat()
 * @method Mage_Directory_Model_Country_Format setFormat(string $value)
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Country_Format extends Magento_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('Mage_Directory_Model_Resource_Country_Format');
    }

}
