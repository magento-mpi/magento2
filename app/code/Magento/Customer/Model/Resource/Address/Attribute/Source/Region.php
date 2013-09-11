<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer region attribute source
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Resource\Address\Attribute\Source;

class Region extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * Retreive all region options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('Magento\Directory\Model\Resource\Region\Collection')
                ->load()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
