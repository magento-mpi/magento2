<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_TypeTest extends PHPUnit_Framework_TestCase
{
   /**
    * Emulate string translation
    *
    * @param mixed $text
    * @return string
    */
    public function translate($text)
    {
        return ($text . '_translate');
    }

    /**
    * test getAllOptions method
    */
    public function testGetAllOptions()
    {
        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_Type')
            ->setMethods(array('_getHelper'))
            ->getMock();

        $helperMock = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();

        $helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnCallback(array($this, 'translate')));

        $model->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helperMock));

        $expected = array(
            'invoice' => $this->translate('Invoice'),
            'creditmemo' => $this->translate('Credit Memo'),
            'shipment' => $this->translate('Shipment'),
        );

        $options = $model->getAllOptions();
        foreach ($expected as $key => $label) {
            $this->assertArrayHasKey($key, $options);
            $this->assertEquals($label, $options[$key]);
        }
    }
}
