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

class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Backend_Model_Config $backendConfigModel
     * @param string $locale
     * @return Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler
     */
    protected function _getFlatRateSaveHandlerInstance(
        Magento_Core_Model_Config $config,
        Magento_Backend_Model_Config $backendConfigModel,
        $locale
    ) {
        $localeMock = $this->getMockBuilder('Magento_Core_Model_Locale')
            ->setMethods(array('getLocale'))
            ->disableOriginalConstructor()
            ->getMock();

        $localeMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue(new Zend_Locale($locale)));

        $validator = new Magento_Validator_Float();

        return new Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler(
            $config,
            $backendConfigModel,
            $localeMock,
            $validator
        );
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
        return $this->_getFlatRateSaveHandlerInstance($config, $backendConfigModel, 'en_US');
    }

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    public function prepareDataValidInputDataProvider()
    {
        // data set #0
        $data0 = array();
        $data0['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data0['groups']['flatrate']['fields']['type']['value'] = 'O';
        $data0['groups']['flatrate']['fields']['price']['value'] = ' 10.11 ';
        $data0['groups']['flatrate']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['flatrate']['fields']['name']['value'] = 'flate rate method name';
        $preparedData0['carriers']['flatrate']['fields']['type']['value'] = 'O';
        $preparedData0['carriers']['flatrate']['fields']['price']['value'] = '10.11';
        $preparedData0['carriers']['flatrate']['fields']['active']['value'] = 1;

        // data set #1
        $data1 = $data0;
        $preparedData1 = $preparedData0;
        $data1['groups']['flatrate']['fields']['type']['value'] = 'I';
        $preparedData1['carriers']['flatrate']['fields']['type']['value'] = 'I';

        // data set #2
        $data2 = $data0;
        $preparedData2 = $preparedData0;
        $data2['groups']['flatrate']['fields']['price']['value'] = '0';
        $preparedData2['carriers']['flatrate']['fields']['price']['value'] = '0';
        return array(
            array($data0, $preparedData0, array('carriers')),
            array($data1, $preparedData1, array('carriers')),
            array($data2, $preparedData2, array('carriers')),
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

        $data1 = array();
        $data1['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data1['groups']['flatrate']['fields']['type']['value'] = 'K'; // wrong type
        $data1['groups']['flatrate']['fields']['price']['value'] = ' 10.11 ';

        $data2 = array();
        // name is absent
        $data2['groups']['flatrate']['fields']['type']['value'] = 'O';
        $data2['groups']['flatrate']['fields']['price']['value'] = ' 10.11 ';

        $data3 = array();
        // type is absent
        $data3['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data3['groups']['flatrate']['fields']['price']['value'] = ' 10.11 ';

        $data4 = array();
        // price is absent
        $data4['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data4['groups']['flatrate']['fields']['type']['value'] = 'O';

        $data5 = array();
        $data5['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data5['groups']['flatrate']['fields']['type']['value'] = 'O';
        $data5['groups']['flatrate']['fields']['price']['value'] = ' price '; // wrong price

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
            array($data4),
            array($data5),
        );
    }

    /**
     * @dataProvider prepareDataForDifferentLocalesDataProvider
     * @param array $data
     * @param array $expectedResult
     * @param string $configSection
     * @param $locale
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testPrepareDataForDifferentLocales(array $data, array $expectedResult, $configSection, $locale)
    {
        $backendConfigModel = $this->getMockBuilder('Magento_Backend_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $config = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $saveHandler = $this->_getFlatRateSaveHandlerInstance($config, $backendConfigModel, $locale);
        $this->assertEquals($expectedResult, $saveHandler->prepareData($data));
    }

    public function prepareDataForDifferentLocalesDataProvider()
    {
        $data = $this->prepareDataValidInputDataProvider();
        $data[0][0]['groups']['flatrate']['fields']['price']['value'] = '11,15';
        $data[0][1]['carriers']['flatrate']['fields']['price']['value'] = '11.15';
        $data[0][] = 'uk_UA';
        $data[1][0]['groups']['flatrate']['fields']['price']['value'] = '1,978.13';
        $data[1][1]['carriers']['flatrate']['fields']['price']['value'] = '1978.13';
        $data[1][] = 'en_US';
        $data[2][] = 'en_US';
        return $data;
    }
}
