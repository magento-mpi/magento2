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
 * Migration logger. Output result print to console
 */
class Tools_Migration_System_Configuration_Logger_Console extends Tools_Migration_System_Configuration_LoggerAbstract
{
    /**
     * Print logs to console
     */
    public function report()
    {
        echo $this;
    }
}
