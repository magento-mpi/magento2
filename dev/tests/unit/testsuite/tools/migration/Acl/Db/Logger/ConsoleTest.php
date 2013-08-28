<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/Acl/Db/LoggerAbstract.php';
require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/Acl/Db/Logger/Console.php';

class Tools_Migration_Acl_Db_Logger_ConsoleTest extends PHPUnit_Framework_TestCase
{
    public function testReport()
    {
        $this->expectOutputRegex('/^Mapped items count: 0(.)*/');
        $model = new Tools_Migration_Acl_Db_Logger_Console();
        $model->report();
    }
}

