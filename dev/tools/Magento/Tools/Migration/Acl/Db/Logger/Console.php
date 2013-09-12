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
 * Db migration logger. Output result print to console
 */
class Magento_Tools_Migration_Acl_Db_Logger_Console extends Magento_Tools_Migration_Acl_Db_LoggerAbstract
{
    /**
     * Print logs to console
     */
    public function report()
    {
        echo $this;
    }
}
