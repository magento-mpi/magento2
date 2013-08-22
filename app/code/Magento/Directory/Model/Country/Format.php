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
 * Directory country format model
 *
 * @method Magento_Directory_Model_Resource_Country_Format _getResource()
 * @method Magento_Directory_Model_Resource_Country_Format getResource()
 * @method string getCountryId()
 * @method Magento_Directory_Model_Country_Format setCountryId(string $value)
 * @method string getType()
 * @method Magento_Directory_Model_Country_Format setType(string $value)
 * @method string getFormat()
 * @method Magento_Directory_Model_Country_Format setFormat(string $value)
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Country_Format extends Magento_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('Magento_Directory_Model_Resource_Country_Format');
    }

}
