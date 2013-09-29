<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer store_id attribute source
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute\Source;

class Store extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * Retrieve Full Option values array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = \Mage::getResourceModel('Magento\Core\Model\Resource\Store\Collection')
                ->load()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
