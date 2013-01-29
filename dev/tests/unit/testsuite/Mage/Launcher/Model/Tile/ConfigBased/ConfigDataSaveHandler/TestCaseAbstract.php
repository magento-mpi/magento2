<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract test case for configuration data save handlers tests
 */
abstract class Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandler_TestCaseAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerAbstract
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock('Mage_Backend_Model_Config', array(), array(), '', false);
        // Mock core configuration model
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_saveHandler = $this->getSaveHandlerInstance($config, $backendConfigModel);
    }

    protected function tearDown()
    {
        $this->_saveHandler = null;
    }

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerAbstract
     */
    abstract public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    );

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    abstract public function prepareDataValidInputDataProvider();

    /**
     * This data provider emulates invalid input for prepareData method
     *
     * @return array
     */
    abstract public function prepareDataInvalidInputDataProvider();

    /**
     * @dataProvider prepareDataValidInputDataProvider
     * @param array $data
     * @param array $expectedResult
     * @param string $configSection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testPrepareData(array $data, array $expectedResult, $configSection)
    {
        $this->assertEquals($expectedResult, $this->_saveHandler->prepareData($data));
    }

    /**
     * @dataProvider prepareDataInvalidInputDataProvider
     * @expectedException Mage_Launcher_Exception
     * @param array $data
     */
    public function testPrepareDataThrowsExceptionWhenInputDataIsInvalid(array $data)
    {
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @dataProvider prepareDataValidInputDataProvider
     * @param array $data
     * @param array $preparedData
     * @param string $configSection
     */
    public function testSave(array $data, array $preparedData, $configSection)
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock(
            'Mage_Backend_Model_Config',
            array('setSection', 'setGroups', 'save'),
            array(),
            '',
            false
        );

        $backendConfigModel->expects($this->once())
            ->method('setSection')
            ->with($configSection)
            ->will($this->returnValue($backendConfigModel));

        $backendConfigModel->expects($this->once())
            ->method('setGroups')
            ->with($preparedData)
            ->will($this->returnValue($backendConfigModel));

        $backendConfigModel->expects($this->once())
            ->method('save');
        // Mock core configuration model
        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('reinit'),
            array(),
            '',
            false
        );

        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnValue($config));

        $saveHandler = $this->getSaveHandlerInstance($config, $backendConfigModel);
        $saveHandler->save($data);
    }

    /**
     * @dataProvider prepareDataInvalidInputDataProvider
     * @expectedException Mage_Launcher_Exception
     * @param array $data
     */
    public function testSaveDoesNotCatchExceptionsThrownByPrepareData(array $data)
    {
        $this->_saveHandler->save($data);
    }
}
