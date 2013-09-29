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
namespace Magento\Tools\Migration\System\Configuration\Logger;

class Console
    extends \Magento\Tools\Migration\System\Configuration\LoggerAbstract
{
    /**
     * Print logs to console
     */
    public function report()
    {
        echo $this;
    }
}
