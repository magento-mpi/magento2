<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

class DependencyManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Module\DependencyManager
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\Module\DependencyManager();
    }

    /**
     * @param array $moduleConfig
     * @dataProvider checkModuleDependenciesDataProvider
     * @expectedException \Exception
     * @expectedExceptionMessage Module 'Module_One' depends on 'Module_Two' that is missing or not active.
     */
    public function testCheckModuleDependenciesNegativeModuleMissed(array $moduleConfig)
    {
        $moduleConfig['dependencies']['modules'][] = 'Module_Two';
        $this->model->checkModuleDependencies($moduleConfig, array('Module_Three'));
    }

    /**
     * @param array $moduleConfig
     * @dataProvider checkModuleDependenciesDataProvider
     * @expectedException \Exception
     * @expectedExceptionMessage Module 'Module_One' depends on 'custom_extension' PHP extension that is not loaded.
     */
    public function testCheckModuleDependenciesNegativeExtensionMissed(array $moduleConfig)
    {
        $moduleConfig['dependencies']['extensions']['strict'][] = array('name' => 'custom_extension');
        $this->model->checkModuleDependencies($moduleConfig);
    }

    /**
     * @param array $moduleConfig
     * @dataProvider checkModuleDependenciesDataProvider
     * @expectedException \Exception
     * @expectedExceptionMessage Module 'Module_One' depends on at least one of the following PHP extensions: custom_extension1,custom_extension2.
     */
    public function testCheckModuleDependenciesNegativeAlternativeExtensionMissed(array $moduleConfig)
    {
        $moduleConfig['dependencies']['extensions']['alternatives'][] = array(
            array('name' => 'custom_extension1'),
            array('name' => 'custom_extension2'),
        );
        $this->model->checkModuleDependencies($moduleConfig);
    }

    /**
     * return array
     */
    public function checkModuleDependenciesDataProvider()
    {
        return array(
            array(
                'moduleConfig' => array(
                    'name' => 'Module_One',
                    'version' => '1.0.0.0',
                    'active' => true,
                    'dependencies' => array(
                        'modules' => array(),
                        'extensions' => array(
                            'strict' => array(),
                            'alternatives' => array(),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * @param array $modules
     * @dataProvider getExtendedModuleDependenciesDataProvider
     * @expectedException \Exception
     * @expectedExceptionMessage Module 'Module_Two' cannot depend on 'Module_One' since it creates circular dependency.
     */
    public function testGetExtendedModuleDependenciesNegativeCircularDependency(array $modules)
    {
        $modules['Module_Two']['dependencies']['modules'][] = 'Module_One';
        $this->model->getExtendedModuleDependencies('Module_One', $modules);
    }

    /**
     * @param array $modules
     * @dataProvider getExtendedModuleDependenciesDataProvider
     */
    public function testGetExtendedModuleDependenciesPositive(array $modules)
    {
        $this->assertEquals(array('Module_Two'), $this->model->getExtendedModuleDependencies('Module_One', $modules));
    }

    /**
     * return array
     */
    public function getExtendedModuleDependenciesDataProvider()
    {
        return array(
            array(
                'modules' => array(
                    'Module_One' => array(
                        'name' => 'Module_One',
                        'version' => '1.0.0.0',
                        'active' => true,
                        'dependencies' => array(
                            'modules' => array('Module_Two'),
                            'extensions' => array(
                                'strict' => array(),
                                'alternatives' => array(),
                            ),
                        ),
                    ),
                    'Module_Two' => array(
                        'name' => 'Module_Two',
                        'version' => '1.0.0.0',
                        'active' => true,
                        'dependencies' => array(
                            'modules' => array(),
                            'extensions' => array(
                                'strict' => array(),
                                'alternatives' => array(),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}
