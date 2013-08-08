<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Directory_Model_Config_Source_Country_Full extends Mage_Directory_Model_Config_Source_Country
    implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray($isMultiselect=false) {
        return parent::toOptionArray(true);
    }
}
