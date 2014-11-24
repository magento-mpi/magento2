<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Cache;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $writer;

    protected function setUp()
    {
        $this->config = $this->getMock('Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $this->writer = $this->getMock('Magento\Framework\App\DeploymentConfig\Writer', [], [], '', false);
    }

    /**
     * @param string $cacheType
     * @param array $config
     * @param bool $banAll
     * @param bool $expectedIsEnabled
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($cacheType, $config, $banAll, $expectedIsEnabled)
    {
        $model = new State($this->config, $this->writer, $banAll);
        if ($banAll) {
            $this->config->expects($this->never())->method('getSegment');
        } else {
            $this->config->expects($this->once())->method('getSegment')->willReturn($config);
        }
        $this->writer->expects($this->never())->method('update');
        $actualIsEnabled = $model->isEnabled($cacheType);
        $this->assertEquals($expectedIsEnabled, $actualIsEnabled);
    }

    /**
     * @return array
     */
    public static function isEnabledDataProvider()
    {
        return array(
            'enabled' => array(
                'cacheType' => 'cache_type',
                'config' => array('some_type' => false, 'cache_type' => true),
                'banAll' => false,
                'expectedIsEnabled' => true
            ),
            'disabled' => array(
                'cacheType' => 'cache_type',
                'config' => array('some_type' => true, 'cache_type' => false),
                'banAll' => false,
                'expectedIsEnabled' => false
            ),
            'unknown is disabled' => array(
                'cacheType' => 'unknown_cache_type',
                'config' => array('some_type' => true),
                'banAll' => false,
                'expectedIsEnabled' => false
            ),
            'disabled, when all caches are banned' => array(
                'cacheType' => 'cache_type',
                'config' => array('cache_type' => true),
                'banAll' => true,
                'expectedIsEnabled' => false
            )
        );
    }

    public function testSetEnabled()
    {
        $model = new State($this->config, $this->writer);
        $this->assertFalse($model->isEnabled('cache_type'));
        $model->setEnabled('cache_type', true);
        $this->assertTrue($model->isEnabled('cache_type'));
        $model->setEnabled('cache_type', false);
        $this->assertFalse($model->isEnabled('cache_type'));
    }

    public function testPersist()
    {
        $model = new State($this->config, $this->writer);
        $constraint = new \PHPUnit_Framework_Constraint_IsInstanceOf('Magento\Framework\App\Cache\Type\ConfigSegment');
        $this->writer->expects($this->once())->method('update')->with($constraint);
        $model->persist();
    }
}
