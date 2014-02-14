<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report;

use Magento\Tools\Dependency\ServiceLocator;

class CircularTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    protected $builder;

    protected function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../_files') . '/';
        $this->sourceFilename = $this->fixtureDir . 'circular-dependencies.csv';

        $this->builder = ServiceLocator::getCircularDependenciesReportBuilder();
    }

    public function testBuild()
    {
        $this->builder->build([
            'files_for_parse' => [
                $this->fixtureDir . 'config4.xml',
                $this->fixtureDir . 'config5.xml',
            ],
            'report_filename' => $this->sourceFilename,
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/circular-dependencies.csv',
            $this->sourceFilename
        );
    }

    public function testBuildWithoutDependencies()
    {
        $this->builder->build([
            'files_for_parse' => [
                $this->fixtureDir . 'config3.xml',
            ],
            'report_filename' => $this->sourceFilename,
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/without-circular-dependencies.csv',
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
