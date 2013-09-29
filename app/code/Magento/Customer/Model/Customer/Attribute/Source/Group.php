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
 * Customer group attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Source;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('Magento\Customer\Model\Resource\Group\Collection')
                ->setRealGroupsFilter()
                ->load()
                ->toOptionArray()
            ;
        }
        return $this->_options;
    }
}
