<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Directory_Model_Config_Source_Country_Full extends Mage_Directory_Model_Config_Source_Country
{
    public function toOptionArray($isMultiselect=false) {
        return parent::toOptionArray(true);
    }
}
