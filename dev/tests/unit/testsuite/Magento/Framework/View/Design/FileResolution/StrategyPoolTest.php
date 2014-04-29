<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Design\FileResolution;

use Magento\Framework\App\State;

/**
 * StrategyPool Test
 *
 * @package Magento\Framework\View
 */
class StrategyPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appState;

    /**
     * @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var StrategyPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager', array(), array(), '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->filesystem = $this->getMock('Magento\Framework\App\Filesystem', array('getPath'), array(), '', false);
        $pathMap = array(
            array(\Magento\Framework\App\Filesystem::VAR_DIR, 'base_dir/var'),
            array(\Magento\Framework\App\Filesystem::ROOT_DIR, 'base_dir')
        );
        $this->filesystem->expects($this->any())->method('getPath')->will($this->returnValueMap($pathMap));

        $this->model = new StrategyPool($this->objectManager, $this->appState, $this->filesystem);
    }

    /**
     * Test, that strategy creation works and a strategy is returned.
     *
     * Do not test exact strategy returned, as it depends on configuration, which can be changed any time.
     *
     * @param string $mode
     * @dataProvider getStrategyDataProvider
     */
    public function testGetStrategy($mode)
    {
        // 3 similar methods tested at once
        $this->appState->expects($this->exactly(3))->method('getMode')->will($this->returnValue($mode));

        $strategy = new \StdClass();
        $mapDir = 'base_dir/var/' . StrategyPool::FALLBACK_MAP_DIR;
        $map = array(
            array(
                'Magento\Framework\View\Design\FileResolution\Strategy\Fallback\CachingProxy',
                array('mapDir' => $mapDir, 'baseDir' => 'base_dir'),
                $strategy
            ),
            array('Magento\Framework\View\Design\FileResolution\Strategy\Fallback', array(), $strategy)
        );
        $this->objectManager->expects($this->atLeastOnce())->method('create')->will($this->returnValueMap($map));

        // Test
        $this->assertSame($strategy, $this->model->getFileStrategy());
        $this->assertSame($strategy, $this->model->getLocaleStrategy());
        $this->assertSame($strategy, $this->model->getViewStrategy());
    }

    /**
     * @return array
     */
    public static function getStrategyDataProvider()
    {
        return array(
            'default mode' => array(State::MODE_DEFAULT),
            'production mode' => array(State::MODE_PRODUCTION),
            'developer mode' => array(State::MODE_DEVELOPER)
        );
    }
}
