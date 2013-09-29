<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Migration\Acl\Db\Logger;

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/LoggerAbstract.php';

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Logger/Console.php';

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testReport()
    {
        $this->expectOutputRegex('/^Mapped items count: 0(.)*/');
        $model = new \Magento\Tools\Migration\Acl\Db\Logger\Console();
        $model->report();
    }
}

