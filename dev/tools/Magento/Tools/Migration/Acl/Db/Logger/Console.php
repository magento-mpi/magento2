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
namespace Magento\Tools\Migration\Acl\Db\Logger;

class Console extends \Magento\Tools\Migration\Acl\Db\LoggerAbstract
{
    /**
     * Print logs to console
     */
    public function report()
    {
        echo $this;
    }
}
