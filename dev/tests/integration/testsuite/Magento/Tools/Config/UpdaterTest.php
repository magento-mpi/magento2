<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Config;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Shell
     */
    protected $shell;

    public function setUp()
    {
        $this->shell = new \Magento\Shell(new \Magento\OSInfo());
    }

    /**
     * @dataProvider diUpdaterDataProvider
     */
    public function testDiUpdater($file, $expectedResult)
    {
        $this->markTestSkipped('Case sensitive script name problem');
        try {
            $result = $this->shell->execute(
                'php -f %s -- --f %s --o',
                array(BP . '/dev/tools/Magento/Tools/Config/update.php', $file)
            );

            $result = str_replace(PHP_EOL, "\n", $result);
            $this->assertEquals($expectedResult, $result);
        } catch (\Magento\Exception $exception) {
            $this->fail($exception->getPrevious()->getMessage());
        }
    }

    public function diUpdaterDataProvider()
    {
        $filesPath = __DIR__ . '/_files/';
        return array(
            array($filesPath . '/Test.php', file_get_contents($filesPath . '/Expected.php'), ''),
        );
    }
}
