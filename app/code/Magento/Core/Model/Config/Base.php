<?php
/**
 * Abstract configuration class
 * Used to retrieve core configuration values
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class Base extends \Magento\Simplexml\Config
{
    /**
     * List of instances
     *
     * @var Base[]
     */
    public static $instances = array();

    /**
     * @param string|\Magento\Simplexml\Element $sourceData $sourceData
     */
    public function __construct($sourceData = null)
    {
        $this->_elementClass = 'Magento\Core\Model\Config\Element';
        parent::__construct($sourceData);
        self::$instances[] = $this;
    }

    /**
     * Cleanup objects because of simplexml memory leak
     *
     * @return void
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
