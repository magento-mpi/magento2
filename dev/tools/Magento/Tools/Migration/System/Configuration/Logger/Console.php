<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Migration logger. Output result print to console
 */
class Magento_Tools_Migration_System_Configuration_Logger_Console
    extends Magento_Tools_Migration_System_Configuration_LoggerAbstract
{
    /**
     * Print logs to console
     */
    public function report()
    {
        echo $this;
    }
}
