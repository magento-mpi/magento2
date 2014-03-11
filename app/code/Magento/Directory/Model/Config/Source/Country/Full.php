<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Directory\Model\Config\Source\Country;

class Full extends \Magento\Directory\Model\Config\Source\Country
    implements \Magento\Option\ArrayInterface
{
    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect=false) {
        return parent::toOptionArray(true);
    }
}
