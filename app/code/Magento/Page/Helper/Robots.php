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
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig
    ) {
        parent::__construct(
            $context
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Get default value of custom instruction in robots.txt from config
     *
     * @return string
     */
    public function getRobotsDefaultCustomInstructions()
    {
        return trim((string)$this->_coreConfig->getValue(self::XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS, 'default'));
    }
}
