<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig\Writer;

class PhpFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $formatter = new PhpFormatter;
        $data = 'test';
        $this->assertEquals("<?php\nreturn 'test';\n", $formatter->format($data));
    }
}
