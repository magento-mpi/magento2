<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation backend config cache model
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Model_Adminhtml_System_Config_Backend_Cache
    extends Magento_Backend_Model_Config_Backend_Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        Magento_Backend_Block_Menu::CACHE_TAGS
    );
}
