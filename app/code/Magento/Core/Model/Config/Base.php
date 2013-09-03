<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract configuration class
 *
 * Used to retrieve core configuration values
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Core_Model_Config_Base extends \Magento\Simplexml\Config implements Magento_Core_Model_ConfigInterface
{
    /**
     * List of instances
     *
     * @var array
     */
    public static $instances = array();

    /**
     * @param string|\Magento\Simplexml\Element $sourceData $sourceData
     */
    public function __construct($sourceData = null)
    {
        $this->_elementClass = 'Magento_Core_Model_Config_Element';
        parent::__construct($sourceData);
        self::$instances[] = $this;
    }

    /**
     * Reinitialize config object
     */
    public function reinit()
    {

    }

    /**
     * Cleanup objects because of simplexml memory leak
     */
    public static function destroy()
    {
        if (is_array(self::$instances)) {
            foreach (self::$instances  as $instance) {
                $instance->_xml = null;
            }
        }
        self::$instances = array();
    }
}
