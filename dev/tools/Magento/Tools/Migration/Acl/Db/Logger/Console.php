<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Migration\Acl\Db\Logger;

/**
 * Db migration logger. Output result print to console
 */
class Console extends \Magento\Tools\Migration\Acl\Db\AbstractLogger
{
    /**
     * Print logs to console
     *
     * @return void
     */
    public function report()
    {
        echo $this;
    }
}
