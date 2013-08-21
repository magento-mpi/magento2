<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper for "Search Engine Robots" functionality
 *
 * @category   Magento
 * @package    Magento_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Helper_Robots extends Magento_Core_Helper_Abstract
{
    const XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS =
        'default/design/search_engine_robots/default_custom_instructions';

    /**
     * Get default value of custom instruction in robots.txt from config
     *
     * @return string
     */
    public function getRobotsDefaultCustomInstructions()
    {
        return trim((string)Mage::getConfig()->getNode(self::XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS));
    }
}
