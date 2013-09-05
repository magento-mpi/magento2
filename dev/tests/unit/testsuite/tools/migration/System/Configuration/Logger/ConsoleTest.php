<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/LoggerAbstract.php';
require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/Logger/Console.php';

class Tools_Migration_System_Configuration_Logger_ConsoleTest extends PHPUnit_Framework_TestCase
{
    public function testReport()
    {
        $this->expectOutputRegex('/^valid: 0(.)*/');
        $model = new Tools_Migration_System_Configuration_Logger_Console();
        $model->report();
    }
}

