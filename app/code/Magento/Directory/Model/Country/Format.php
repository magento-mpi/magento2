<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Model\Country;

/**
 * \Directory country format model
 *
 * @method \Magento\Directory\Model\Resource\Country\Format _getResource()
 * @method \Magento\Directory\Model\Resource\Country\Format getResource()
 * @method string getCountryId()
 * @method \Magento\Directory\Model\Country\Format setCountryId(string $value)
 * @method string getType()
 * @method \Magento\Directory\Model\Country\Format setType(string $value)
 * @method string getFormat()
 * @method \Magento\Directory\Model\Country\Format setFormat(string $value)
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Format extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Directory\Model\Resource\Country\Format');
    }
}
