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

class Mage_Launcher_Model_Storelauncher_Design_SaveHandlerTest
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * Helper factory
     *
     * @var Mage_Core_Model_Factory_Helper|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperFactory;

    /**
     * Configuration loader
     *
     * @var Mage_Backend_Model_Config_Loader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configLoader;

    /**
     * Config Writer Model
     *
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * Logo backend config model
     *
     * @var Mage_Backend_Model_Config_Backend_Image_Logo|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelLogo;

    protected function setUp()
    {
        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $store->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('default'));

        $helperMock = $this->getMock('Mage_Launcher_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->any())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($store));
        $helperMock->expects($this->any())
            ->method('getTmpLogoPath')
            ->will($this->returnArgument(0));

        $this->_helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper',
            array(), array(), '', false, false
        );
        $this->_helperFactory->expects($this->any())->method('get')->with('Mage_Launcher_Helper_Data')
            ->will($this->returnValue($helperMock));

        $this->_configLoader = $this->getMock('Mage_Backend_Model_Config_Loader',
            array(), array(), '', false, false
        );
        $this->_configLoader->expects($this->any())
            ->method('getConfigByPath')
            ->with($this->equalTo('design/header'), $this->equalTo(Mage_Core_Model_Config::SCOPE_STORES),
                $this->equalTo(1))
            ->will($this->returnValue(array (
                'design/header/logo_src' => array (
                    'path' => 'design/header/logo_src',
                    'value' => 'default/design_logo_1.png',
                    'config_id' => '69',
                ),
            )));

        $this->_configWriter = $this->getMock('Mage_Core_Model_Config_Storage_WriterInterface',
            array(), array(), '', false, false
        );
        $this->_configWriter->expects($this->any())
            ->method('save')
            ->with($this->equalTo('design/header/logo_src'), $this->isEmpty(), Mage_Core_Model_Config::SCOPE_STORES, 1)
            ->will($this->returnSelf());

        $this->_modelLogo = $this->getMockBuilder('Mage_Backend_Model_Config_Backend_Image_Logo')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'setPath',
                'setConfigId',
                'setScope',
                'setValue',
                'save'
            ))->getMock();
        $this->_modelLogo->expects($this->any())->method('setPath')->with($this->equalTo('design/header/logo_src'))
            ->will($this->returnSelf());
        $this->_modelLogo->expects($this->any())->method('setConfigId')->with($this->equalTo('69'))
            ->will($this->returnSelf());
        $this->_modelLogo->expects($this->any())->method('setScope')
            ->with($this->equalTo(Mage_Core_Model_Config::SCOPE_STORES))
            ->will($this->returnSelf());
        $this->_modelLogo->expects($this->any())->method('setValue')->with($this->equalTo(array(
                'value' => 'dragons.png',
                'tmp_name' =>  'dragons.png',
            )))
            ->will($this->returnSelf());
        $this->_modelLogo->expects($this->any())->method('save')
            ->will($this->returnSelf());

        parent::setUp();
    }

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        return new Mage_Launcher_Model_Storelauncher_Design_SaveHandler(
            $config,
            $backendConfigModel,
            $this->_helperFactory,
            $this->_configLoader,
            $this->_configWriter,
            $this->_modelLogo
        );
    }

    /**
     * This data provider emulates invalid input for prepareData method
     *
     * @return array
     */
    public function prepareDataInvalidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['design']['theme']['fields']['theme_id']['value'] = '';

        $data1 = array();

        return array(
            array($data0),
            array($data1),
        );
    }

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    public function prepareDataValidInputDataProvider()
    {
        return array(
            array(
                $this->_getTestData(),
                $this->_getExpectedData(),
                array('design')
            ),
        );
    }

    /**
     * Get array of test data, emulating request data
     *
     * @return array
     */
    protected function _getTestData()
    {
        $result = array(
            'groups' => array(
                'design' => array(
                    'theme' => array(
                        'fields' => array(
                            'theme_id' => array('value' => '1'),
                        ),
                    ),

                ),
            ),
            'tileCode' => 'design',
            'logo_src' => 'dragons.png'
        );

        return $result;
    }

    /**
     * Get Expected data
     *
     * @return array
     */
    protected function _getExpectedData()
    {
        $result = array(
            'design' => array(
                'theme' => array(
                    'fields' => array(
                        'theme_id' => array('value' => '1'),
                    ),
                ),
            ),
        );
        return $result;
    }
}
