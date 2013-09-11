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
namespace Magento\Page\Helper;

class Robots extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS = 'design/search_engine_robots/default_custom_instructions';

    /**
     * Get default value of custom instruction in robots.txt from config
     *
     * @return string
     */
    public function getRobotsDefaultCustomInstructions()
    {
        return trim((string)\Mage::getConfig()->getValue(self::XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS, 'default'));
    }
}
