<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Cache_Resource_Grid_Options_Statuses extends Mage_Backend_Model_Config_Source_Statuses_Options
{
    /**
     * @var Mage_Index_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Index_Helper_Data $helper
     */
    public function __construct(Mage_Index_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

}