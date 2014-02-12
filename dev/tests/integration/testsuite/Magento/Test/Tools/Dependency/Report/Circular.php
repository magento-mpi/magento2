<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Builder;

use Magento\Tools\Dependency\ServiceLocator;

// TODO:
require_once BP . '/dev/tests/static/framework/Magento/TestFramework/Dependency/Circular.php';

class Circular extends \PHPUnit_Framework_TestCase
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
        $this->sourceFilename = $this->fixtureDir . 'modules-circular-dependencies.csv';

        $this->builder = ServiceLocator::getCircularDependenciesReportBuilder();
    }

    public function testBuild()
    {
        $this->builder->build([
            'configFiles' => [
                $this->fixtureDir . 'config4.xml',
                $this->fixtureDir . 'config5.xml',
            ],
            'filename' => $this->sourceFilename,
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/modules-circular-dependencies.csv',
            $this->sourceFilename
        );
    }

    public function testBuildWithoutDependencies()
    {
        $this->builder->build([
            'configFiles' => [
                $this->fixtureDir . 'config3.xml',
            ],
            'filename' => $this->sourceFilename,
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/modules-without-circular-dependencies.csv',
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
