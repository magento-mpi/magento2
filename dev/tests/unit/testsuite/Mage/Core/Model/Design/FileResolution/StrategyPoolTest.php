<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Design_StrategyPoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_App_State|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appState;

    /**
     * @var Mage_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    /**
     * @var Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Design_FileResolution_StrategyPool|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Mage_Core_Model_ObjectManager', array(), array(), '', false);
        $this->_appState = $this->getMock('Mage_Core_Model_App_State', array(), array(), '', false);

        $this->_dirs = new Mage_Core_Model_Dir('base_dir');

        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);

        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $config->expects($this->any())
            ->method('getNode')
            ->with(Mage_Core_Model_Design_FileResolution_StrategyPool::XML_PATH_ALLOW_MAP_UPDATE)
            ->will($this->returnValue('1'));
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnValue($config));

        $this->_model = new Mage_Core_Model_Design_FileResolution_StrategyPool($this->_objectManager,
            $this->_appState, $this->_dirs, $this->_filesystem);
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
        $this->_appState->expects($this->exactly(3)) // 3 similar methods tested at once
            ->method('getMode')
            ->will($this->returnValue($mode));

        $strategy = new StdClass;
        $mapDir = 'base_dir/var/' . Mage_Core_Model_Design_FileResolution_StrategyPool::FALLBACK_MAP_DIR;
        $mapDir = str_replace('/', DIRECTORY_SEPARATOR, $mapDir);
        $map = array(
            array(
                'Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy',
                array(
                    'mapDir' => $mapDir,
                    'baseDir' => 'base_dir',
                    'canSaveMap' => true
                ),
                $strategy
            ),
            array('Mage_Core_Model_Design_FileResolution_Strategy_Fallback', array(), $strategy),
        );
        $this->_objectManager->expects($this->atLeastOnce())
            ->method('create')
            ->will($this->returnValueMap($map));

        // Test
        $actual = call_user_func(array($this->_model, 'getFileStrategy'));
        $this->assertSame($strategy, $actual);

        $actual = call_user_func(array($this->_model, 'getLocaleStrategy'));
        $this->assertSame($strategy, $actual);

        $actual = call_user_func(array($this->_model, 'getViewStrategy'));
        $this->assertSame($strategy, $actual);
    }

    public static function getStrategyDataProvider()
    {
        return array(
            'default mode' => array(
                Mage_Core_Model_App_State::MODE_DEFAULT
            ),
            'production mode' => array(
                Mage_Core_Model_App_State::MODE_PRODUCTION
            ),
            'developer mode' => array(
                Mage_Core_Model_App_State::MODE_DEVELOPER
            ),
        );
    }
}
