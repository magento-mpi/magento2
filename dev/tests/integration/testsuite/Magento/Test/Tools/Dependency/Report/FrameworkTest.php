<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Tools\Dependency\Report;

use Magento\Tools\Dependency\ServiceLocator;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    /**
     * @var string
     */
    protected $fixtureDirModule;

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
        $this->fixtureDirModule = $this->fixtureDir . 'code/Magento/FirstModule/';
        $this->sourceFilename = $this->fixtureDir . 'framework-dependencies.csv';

        $this->builder = ServiceLocator::getFrameworkDependenciesReportBuilder();
    }

    public function testBuild()
    {
        $this->builder->build(
            array(
                'parse' => array(
                    'files_for_parse' => array(
                        $this->fixtureDirModule . 'Helper/Helper.php',
                        $this->fixtureDirModule . 'Model/Model.php',
                        $this->fixtureDirModule . 'view/frontend/template.phtml'
                    ),
                    'config_files' => array($this->fixtureDirModule . 'etc/module.xml'),
                    'declared_namespaces' => array('Magento')
                ),
                'write' => array('report_filename' => $this->sourceFilename)
            )
        );

        $this->assertFileEquals($this->fixtureDir . 'expected/framework-dependencies.csv', $this->sourceFilename);
    }

    public function testBuildWithoutDependencies()
    {
        $this->builder->build(
            array(
                'parse' => array(
                    'files_for_parse' => array($this->fixtureDirModule . 'Model/WithoutDependencies.php'),
                    'config_files' => array($this->fixtureDirModule . 'etc/module.xml'),
                    'declared_namespaces' => array('Magento')
                ),
                'write' => array('report_filename' => $this->sourceFilename)
            )
        );

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/without-framework-dependencies.csv',
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
