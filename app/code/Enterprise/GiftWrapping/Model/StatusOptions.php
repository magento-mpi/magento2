<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Users
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Enterprise
 * @package    Enterprise_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Model_StatusOptions extends Mage_Backend_Model_Config_Source_Statuses_Options
{
    /**
     * Backend Helper
     *
     * @var Enterprise_GiftWrapping_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_GiftWrapping_Helper_Data $helper
     */
    public function __construct(Enterprise_GiftWrapping_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

}
