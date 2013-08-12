<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract test case for configuration data save handlers tests
 */
abstract class Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock('Magento_Backend_Model_Config', array(), array(), '', false);
        // Mock core configuration model
        $config = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_saveHandler = $this->getSaveHandlerInstance($config, $backendConfigModel);
    }

    protected function tearDown()
    {
        $this->_saveHandler = null;
    }

    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    abstract public function getSaveHandlerInstance(
        Magento_Core_Model_Config $config,
        Magento_Backend_Model_Config $backendConfigModel
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
     * @expectedException Saas_Launcher_Exception
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
     * @param array $configSections
     */
    public function testSave(array $data, array $preparedData, array $configSections)
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock(
            'Magento_Backend_Model_Config',
            array('setSection', 'setGroups', 'save'),
            array(),
            '',
            false
        );

        // set expectations for each config section (setSection, setGroups and save methods must be called in sequence)
        for ($index = 0, $sectionsCount = count($configSections); $index < $sectionsCount; $index++) {
            $backendConfigModel->expects($this->at($index * 3))
                ->method('setSection')
                ->with($configSections[$index])
                ->will($this->returnValue($backendConfigModel));

            $backendConfigModel->expects($this->at($index * 3 + 1))
                ->method('setGroups')
                ->with($preparedData[$configSections[$index]])
                ->will($this->returnValue($backendConfigModel));

            $backendConfigModel->expects($this->at($index * 3 + 2))
                ->method('save');
        }

        // Mock core configuration model
        $config = $this->getMock(
            'Magento_Core_Model_Config',
            array('reinit'),
            array(),
            '',
            false
        );

        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnSelf());

        $saveHandler = $this->getSaveHandlerInstance($config, $backendConfigModel);
        $saveHandler->save($data);
    }

    /**
     * @dataProvider prepareDataInvalidInputDataProvider
     * @expectedException Saas_Launcher_Exception
     * @param array $data
     */
    public function testSaveDoesNotCatchExceptionsThrownByPrepareData(array $data)
    {
        $this->_saveHandler->save($data);
    }
}
