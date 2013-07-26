<?php
/**
 * Api User Resource Options Statuses
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Api_Model_Resource_User_Options_Statuses extends Mage_Backend_Model_Config_Source_Activity_Options
{

    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Backend_Helper_Data $helper
     */
    public function __construct(Mage_Backend_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }
}
