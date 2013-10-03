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
    implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray($isMultiselect=false) {
        return parent::toOptionArray(true);
    }
}
