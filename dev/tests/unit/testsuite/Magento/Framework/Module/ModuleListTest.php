<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

class ModuleListTest extends \PHPUnit_Framework_TestCase
{
    private static $allFixture = ['foo' => [], 'bar' => []];

    private static $enabledFixture = ['foo' => 1, 'bar' => 0];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $loader;

    /**
     * @var ModuleList
     */
    private $model;

    protected function setUp()
    {
        $this->config = $this->getMock('Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $this->loader = $this->getMock('Magento\Framework\Module\ModuleList\Loader', [], [], '', false);
        $this->model = new ModuleList($this->config, $this->loader);
    }

    public function testGetAll()
    {
        $this->setLoadAllExpectation();
        $this->setLoadConfigExpectation();
        $expected = ['foo' => self::$allFixture['foo']];
        $this->assertSame($expected, $this->model->getAll());
        $this->assertSame($expected, $this->model->getAll()); // second time to ensure loadAll is called once
    }

    /**
     * Prepares expectation for loading deployment configuration
     *
     * @param bool $isExpected
     * @return void
     */
    private function setLoadConfigExpectation($isExpected = true)
    {
        if ($isExpected) {
            $this->config->expects($this->once())->method('getSegment')->willReturn(self::$enabledFixture);
        } else {
            $this->config->expects($this->never())->method('getSegment');
        }
    }

    /**
     * Prepares expectation for loading full list of modules
     *
     * @param bool $isExpected
     * @return void
     */
    private function setLoadAllExpectation($isExpected = true)
    {
        if ($isExpected) {
            $this->loader->expects($this->once())->method('load')->willReturn(self::$allFixture);
        } else {
            $this->loader->expects($this->never())->method('load');
        }
    }
}
