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

class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        $paypalHelperMock = $this->getMock(
            'Saas_Paypal_Helper_Data',
            array('isEcAcceleratedBoarding'),
            array(),
            '',
            false
        );
        return new Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler(
            $config,
            $backendConfigModel,
            $paypalHelperMock
        );
    }

    /**
     * This data provider emulates valid input for saveData method
     *
     * @return array
     */
    public function saveDataValidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = 'test@example.com';

        $savedData0 = array();
        $savedData0['payment']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = 'test@example.com';
        $savedData0['payment']['paypal_alternative_payment_methods']['groups']['express_checkout_us']
            ['groups']['express_checkout_required']['fields']['enable_express_checkout']['value']  = 1;

        return array(
            array($data0, $savedData0, array('payment')),
        );
    }

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    public function prepareDataValidInputDataProvider()
    {
        $data = $this->saveDataValidInputDataProvider();
        $preparedData = array();
        $preparedData['payment']['paypal_alternative_payment_methods']['groups']['express_checkout_us']
            ['groups']['express_checkout_required']['fields']['enable_express_checkout']['value']  = 1;
        $data[] = array(array(), $preparedData, array('payment'), true);
        return $data;
    }


    /**
     * This data provider emulates invalid input for prepareData method
     *
     * @return array
     */
    public function prepareDataInvalidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = 'invalid_email';

        return array(
            array($data0),
        );
    }

    /**
     * @dataProvider prepareDataValidInputDataProvider
     * @param array $data
     * @param array $expectedResult
     * @param string $configSection
     * @param bool $isBoarding
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testPrepareData(array $data, array $expectedResult, $configSection, $isBoarding = false)
    {
        $paypalHelperMock = $this->getMock(
            'Saas_Paypal_Helper_Data', array('isEcAcceleratedBoarding'), array(), '', false
        );
        $paypalHelperMock->expects($this->any())
            ->method('isEcAcceleratedBoarding')
            ->will($this->returnValue($isBoarding));
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $saveHandler = $objectManagerHelper->getObject(
            'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler',
            array('paypalHelper' => $paypalHelperMock)
        );
        $this->assertEquals($expectedResult, $saveHandler->prepareData($data));
    }

    /**
     * @dataProvider saveDataValidInputDataProvider
     * @param array $data
     * @param array $preparedData
     * @param array $configSections
     */
    public function testSave(array $data, array $preparedData, array $configSections)
    {
        parent::testSave($data, $preparedData, $configSections);
    }
}
