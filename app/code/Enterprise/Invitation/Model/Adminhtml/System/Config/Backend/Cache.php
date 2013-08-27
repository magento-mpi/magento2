<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation backend config cache model
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Adminhtml_System_Config_Backend_Cache
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
