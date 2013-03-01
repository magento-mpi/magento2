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

    protected function setUp()
    {
        $helperMock = $this->getMock('Mage_Launcher_Helper_Data', array(), array(), '', false);

        $store = $this->getMock('Mage_Core_Model_Store', array('getConfig'), array(), '', false);

        $store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $store->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('default'));

        $helperMock->expects($this->any())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($store));

        $this->_helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper',
            array(), array(), '', false, false
        );
        $this->_helperFactory->expects($this->any())->method('get')->with('Mage_Launcher_Helper_Data')
            ->will($this->returnValue($helperMock));
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
            $this->_helperFactory
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
