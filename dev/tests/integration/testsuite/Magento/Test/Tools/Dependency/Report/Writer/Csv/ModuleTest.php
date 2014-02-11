<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Writer;

use Magento\Tools\Dependency\Config;
use Magento\Tools\Dependency\Dependency;
use Magento\Tools\Dependency\Module;
use Magento\Tools\Dependency\Report\Writer\Csv\Module as ReportModuleWriter;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    /**
     * @var string
     */
    protected $sourceFilename;

    /**
     * @var \Magento\Tools\Dependency\Report\Writer\Csv\Module
     */
    protected $writer;

    protected function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../../../_files') . '/';
        $this->sourceFilename = $this->fixtureDir . 'modules-dependencies.csv';

        $this->writer = new ReportModuleWriter();
    }

    public function testWrite()
    {
        $config = new Config([
            new Module('Module1', [
                new Dependency('Magento_Core'),
                new Dependency('Magento_Backend', Dependency::TYPE_SOFT),
                new Dependency('Module1'),
            ]),
            new Module('Module2', [
                new Dependency('Magento_Core'),
                new Dependency('Module2'),
            ]),
        ]);
        $this->writer->write($config, $this->sourceFilename);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/modules-dependencies.csv',
            $this->sourceFilename
        );
    }

    public function testWriteWithoutDependencies()
    {
        $config = new Config([
            new Module('Module1', []),
            new Module('Module2', []),
        ]);
        $this->writer->write($config, $this->sourceFilename);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/modules-without-dependencies.csv',
            $this->sourceFilename
        );
    }

    public function tearDown()
    {
        if (file_exists($this->sourceFilename)) {
            unlink($this->sourceFilename);
        }
    }
}
