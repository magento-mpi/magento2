<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_Template_AbstractTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param string $modelConfig_value
     * @dataProvider providerToOptionArray
     */
    public function testToOptionArray($modelConfigValue, $resultExpected)
    {
        $varienObj = new Magento_Object();

        $helperData = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $helperData->expects($this->any())
            ->method('__')
            ->will(
            $this->returnCallback(
                function ($text)
                {
                    return $text . '_test';
                }
            )
        );

        $modelConfig = $this->getMockBuilder('Magento_Core_Model_Config')
            ->setMethods(array('getNode'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelConfig->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue($modelConfigValue));

        $templateAbstract = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_Template_Abstract')
            ->setMethods(array('_getEntityType', '_getTemplate', '_getHelper', '_getConfig'))
            ->getMock();
        $templateAbstract->expects($this->any())
            ->method('_getEntityType')
            ->will($this->returnValue('invoice'));
        $templateAbstract->expects($this->any())
            ->method('_getTemplate')
            ->will($this->returnValue($varienObj));
        $templateAbstract->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helperData));
        $templateAbstract->expects($this->any())
            ->method('_getConfig')
            ->will($this->returnValue($modelConfig));

        $collectionDb = $this->getMockBuilder('Magento_Data_Collection_Db')
            ->setMethods(array('addFieldToFilter', 'toOptionArray'))
            ->disableOriginalConstructor()
            ->getMock();
        $collectionDb->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $collectionDb->expects($this->any())
            ->method('toOptionArray')
            ->will($this->returnValue(array(array('value' => 'test', 'label' => 'test'))));

        $varienObj->setCollection($collectionDb);
        $result = $templateAbstract->toOptionArray();
        $this->assertEquals($resultExpected, $result,
            'Expected array from dataprovider not equal to $result from toOptionArray()');
    }

    public function providerToOptionArray()
    {
        return array(
            array(
                'Printed Invoice',
                array(
                    array('value' => '', 'label' => '%s (Default Template from Locale)_test'),
                    array('value' => 'test', 'label' => 'test')
                )
            ),
            array(
                null,
                array(
                    array('value' => '', 'label' => 'Default Template from Locale_test'),
                    array('value' => 'test', 'label' => 'test')
                )
            )
        );
    }
}
