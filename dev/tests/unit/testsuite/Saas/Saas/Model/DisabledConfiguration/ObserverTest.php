<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_DisabledConfiguration_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $fieldPath
     * @param string $expectedException
     * @param string $exceptionMessage
     * @dataProvider checkConfigSaveAllowedDataProvider
     */
    public function testCheckConfigSaveAllowed($fieldPath, $expectedException, $exceptionMessage)
    {
        // Prepare model
        $isDisabledValueMap = array(
            array('path/to/permitted_field', false),
            array('path/to/disabled_field', true),
        );
        $disabledConfig = $this->getMock('Saas_Saas_Model_DisabledConfiguration_Config', array('isPathDisabled'),
            array(), '', false);
        $disabledConfig->expects($this->once())
            ->method('isPathDisabled')
            ->with($fieldPath)
            ->will($this->returnValueMap($isDisabledValueMap));
        $model = new Saas_Saas_Model_DisabledConfiguration_Observer($disabledConfig);

        // Prepare expectations and run
        if ($expectedException) {
            $this->setExpectedException($expectedException, $exceptionMessage);
        }

        $configDataMock = $this->getMock('Mage_Core_Model_Config_Data', array('getPath'), array(), '', false);
        $configDataMock->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue($fieldPath));
        $event = new Magento_Event(array('config_data' => $configDataMock));
        $observer = new Magento_Event_Observer(array('event' => $event));

        $model->checkConfigSaveAllowed($observer);
    }

    public function checkConfigSaveAllowedDataProvider()
    {
        return array(
            'allowed path' => array(
                'path/to/permitted_field',
                null,
                null,
            ),
            'disabled path' => array(
                'path/to/disabled_field',
                'Saas_Saas_Exception',
                'Modification is not permitted',
            ),
        );
    }
}
