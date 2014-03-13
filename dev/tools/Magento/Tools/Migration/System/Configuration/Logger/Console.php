<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Migration\System\Configuration\Logger;

/**
 * Migration logger. Output result print to console
 */
class Console
    extends \Magento\Tools\Migration\System\Configuration\AbstractLogger
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
