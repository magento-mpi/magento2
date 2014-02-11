<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Builder;

use Magento\Tools\Dependency\ServiceLocator;

class ModulesDependencies extends \PHPUnit_Framework_TestCase
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
        $this->fixtureDir = realpath(__DIR__ . '/../../_files') . '/';
        $this->sourceFilename = $this->fixtureDir . 'modules-dependencies.csv';

        $this->builder = ServiceLocator::getModulesDependenciesReportBuilder();
    }

    public function testBuild()
    {
        $this->builder->build([
            'configFiles' => [
                $this->fixtureDir . 'config1.xml',
                $this->fixtureDir . 'config2.xml',
            ],
            'filename' => $this->sourceFilename,
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/modules-dependencies.csv',
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
