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

class Saas_Launcher_Model_Storelauncher_Design_SaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * Launcher Helper
     *
     * @var Saas_Launcher_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * Configuration loader
     *
     * @var Magento_Backend_Model_Config_Loader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configLoader;

    /**
     * Config Writer Model
     *
     * @var Magento_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * Logo backend config model
     *
     * @var Magento_Backend_Model_Config_Backend_Image_Logo|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelLogo;

    /**
     * Theme Config model
     *
     * @var Mage_Theme_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeConfig;

    /**
     * Theme factory
     *
     * @var Magento_Core_Model_ThemeFactory
     */
    protected $_themeFactory;

    protected function setUp()
    {
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $store->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('default'));

        $this->_helperMock =  $this->getMock('Saas_Launcher_Helper_Data', array(), array(), '', false);
        $this->_helperMock->expects($this->any())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($store));
        $this->_helperMock->expects($this->any())
            ->method('getTmpLogoPath')
            ->will($this->returnArgument(0));

        $this->_configLoader = $this->getMock('Magento_Backend_Model_Config_Loader',
            array(), array(), '', false, false
        );
        $this->_configLoader->expects($this->any())
            ->method('getConfigByPath')
            ->with($this->equalTo('design/header'), $this->equalTo(Magento_Core_Model_Config::SCOPE_STORES),
                $this->equalTo(1))
            ->will($this->returnValue(array (
                Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO => array (
                    'path' => Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO,
                    'value' => 'default/design_logo_1.png',
                    'config_id' => '69',
                ),
            )));

        $this->_configWriter = $this->getMock('Magento_Core_Model_Config_Storage_WriterInterface',
            array(), array(), '', false, false
        );
        $this->_configWriter->expects($this->any())
            ->method('save')
            ->with($this->equalTo(Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO),
                $this->isEmpty(), Magento_Core_Model_Config::SCOPE_STORES, 1)
            ->will($this->returnSelf());

        $this->_modelLogo = $this->getMockBuilder('Magento_Backend_Model_Config_Backend_Image_Logo')
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
            ->with($this->equalTo(Magento_Core_Model_Config::SCOPE_STORES))
            ->will($this->returnSelf());
        $this->_modelLogo->expects($this->any())->method('setValue')->with($this->equalTo(array(
                'value' => 'dragons.png',
                'tmp_name' =>  'dragons.png',
            )))
            ->will($this->returnSelf());
        $this->_modelLogo->expects($this->any())->method('save')
            ->will($this->returnSelf());

        $themeMock = $this->getMockBuilder('Magento_Core_Model_Theme')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'isVirtual'))
            ->getMock();
        $themeMock->expects($this->any())->method('getId')->will($this->returnValue(20));
        $themeMock->expects($this->any())->method('isVirtual')->will($this->returnValue(true));

        $this->_themeConfig = $this->getMockBuilder('Mage_Theme_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('assignToStore'))
            ->getMock();
        $this->_themeConfig->expects($this->any())->method('assignToStore')
            ->with($themeMock, $this->equalTo(array(1)))
            ->will($this->returnValue($this->_themeConfig));

        $this->_themeFactory = $this->getMock(
            'Magento_Core_Model_ThemeFactory', array('create', 'load'), array(), '', false
        );

        $this->_themeFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_themeFactory));

        $this->_themeFactory->expects($this->any())
            ->method('load')
            ->will($this->returnValue($themeMock));

        parent::setUp();
    }

    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Magento_Core_Model_Config $config,
        Magento_Backend_Model_Config $backendConfigModel
    ) {
        return new Saas_Launcher_Model_Storelauncher_Design_SaveHandler(
            $config,
            $backendConfigModel,
            $this->_helperMock,
            $this->_configLoader,
            $this->_configWriter,
            $this->_modelLogo,
            $this->_themeConfig,
            $this->_themeFactory
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
                        'theme_id' => array('value' => '20'),
                    ),
                ),
            ),
        );
        return $result;
    }
}
