<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Dependency;

class PhpRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhpRule
     */
    protected $model;

    protected function setUp()
    {
        $mapRoutes = array('someModule' => array('Magento\SomeModule'), 'anotherModule' => array('Magento\OneModule'));
        $mapLayoutBlocks = array('area' => array('block.name' => array('Magento\SomeModule' => 'Magento\SomeModule')));
        $this->model = new PhpRule($mapRoutes, $mapLayoutBlocks);
    }

    public function testNonPhpGetDependencyInfo()
    {
        $content = 'any content';
        $this->assertEmpty($this->model->getDependencyInfo('any', 'not php', 'any', $content));
    }

    /**
     * @param string $module
     * @param string $content
     * @param array $expected
     * @dataProvider getDependencyInfoDataProvider
     */
    public function testGetDependencyInfo($module, $content, array $expected)
    {
        $this->assertEquals($expected, $this->model->getDependencyInfo($module, 'php', 'any', $content));
    }

    public function getDependencyInfoDataProvider()
    {
        return array(
            array('Magento\SomeModule', 'something extends \Magento\SomeModule\Any\ClassName {', array()),
            array(
                'Magento\AnotherModule',
                'something extends \Magento\SomeModule\Any\ClassName {',
                array(
                    array(
                        'module' => 'Magento\SomeModule',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'Magento\SomeModule\Any\ClassName'
                    )
                )
            ),
            array(
                'Magento\SomeModule',
                '$this->getViewFileUrl("Magento_SomeModule::js/order-by-sku-failure.js")',
                array()
            ),
            array(
                'Magento\AnotherModule',
                '$this->getViewFileUrl("Magento_SomeModule::js/order-by-sku-failure.js")',
                array(
                    array(
                        'module' => 'Magento\SomeModule',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'Magento_SomeModule'
                    )
                )
            ),
            array('Magento\SomeModule', '$this->helper("Magento\SomeModule\Any\ClassName")', array()),
            array(
                'Magento\AnotherModule',
                '$this->helper("Magento\SomeModule\Any\ClassName")',
                array(
                    array(
                        'module' => 'Magento\SomeModule',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'Magento\SomeModule\Any\ClassName'
                    )
                )
            ),
            array('Magento\SomeModule', '$this->getUrl("someModule")', array()),
            array(
                'Magento\AnotherModule',
                '$this->getUrl("anotherModule")',
                array(
                    array(
                        'module' => 'Magento\OneModule',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'getUrl("anotherModule"'
                    )
                )
            ),
            array('Magento\SomeModule', '$this->getLayout()->getBlock(\'block.name\');', array()),
            array(
                'Magento\AnotherModule',
                '$this->getLayout()->getBlock(\'block.name\');',
                array(
                    array(
                        'module' => 'Magento\SomeModule',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'getBlock(\'block.name\')'
                    )
                )
            )
        );
    }

    /**
     * @param string $module
     * @param string $content
     * @param array $expected
     * @dataProvider getDefaultModelDependencyDataProvider
     */
    public function testGetDefaultModelDependency($module, $content, array $expected)
    {
        $mapLayoutBlocks = array(
            'default' => array(
                'block.name' => array(
                    'Magento\SomeModule' => 'Magento\SomeModule',
                    'Magento\Install' => 'Magento\Install'
                )
            )
        );
        $this->model = new PhpRule(array(), $mapLayoutBlocks);
        $this->assertEquals($expected, $this->model->getDependencyInfo($module, 'template', 'any', $content));
    }

    public function getDefaultModelDependencyDataProvider()
    {
        return array(
            array(
                'Magento\AnotherModule',
                '$this->getLayout()->getBlock(\'block.name\');',
                array(
                    array(
                        'module' => 'Magento\Install',
                        'type' => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => 'getBlock(\'block.name\')'
                    )
                )
            )
        );
    }
}
