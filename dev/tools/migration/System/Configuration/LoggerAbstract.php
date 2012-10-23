<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * System configuration migration logger
 */
abstract class Tools_Migration_System_Configuration_LoggerAbstract
{
    /**
     * List of logs
     *
     * @var array
     */
    protected $_logs = array();

    /**
     * Add log data
     *
     * @param string $message
     * @return Tools_Migration_System_Configuration_LoggerAbstract
     */
    public function add($message)
    {
        $this->_logs[] = $message;
        return $this;
    }

    /**
     * Convert logger object to string
     *
     * @return string
     */
    public function __toString()
    {
       return implode(PHP_EOL, $this->_logs);
    }

    /**
     * Generate report
     *
     * @abstract
     * @return mixed
     */
    public abstract function report();
}
