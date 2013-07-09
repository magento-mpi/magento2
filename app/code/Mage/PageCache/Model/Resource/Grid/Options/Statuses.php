<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache Options Statuses
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_PageCache_Model_Resource_Grid_Options_Statuses extends Mage_Backend_Model_Config_Source_Statuses_Options
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