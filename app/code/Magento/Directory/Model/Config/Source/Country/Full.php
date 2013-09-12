<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Directory_Model_Config_Source_Country_Full extends Magento_Directory_Model_Config_Source_Country
    implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray($isMultiselect=false) {
        return parent::toOptionArray(true);
    }
}
