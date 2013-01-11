<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_PageSizeTest extends PHPUnit_Framework_TestCase
{

    public function translate($text)
    {
        return $text;
    }

   /**
    * test toOptionArray method
    *
    * @param string $sizes
    * @params array $expected
    *
    * @dataProvider testToOptionArrayProvider
    */
    public function testToOptionArray($sizes, $expected)
    {
        $configMock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getConfigSectionArray'))
            ->getMock();

        $configMock->expects($this->any())
            ->method('getConfigSectionArray')
            ->with($this->equalTo('page_size'))
            ->will($this->returnValue($sizes));

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_PageSize')
            ->setMethods(array('_getConfigModel', '_getHelper', ))
            ->getMock();

        $model->expects($this->once())
            ->method('_getConfigModel')
            ->will($this->returnValue($configMock));

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

        $optionArray = $model->toOptionArray();
		$this->assertSame($expected, $optionArray);
    }

   /**
    * provide data for toOptionArrayProvider method
    *
    * @return array
    */
    public function testToOptionArrayProvider()
    {
        return array(
            array(
                array(
                    'a4' => array('label' => 'A4','value' => 0),
                    'letter' => array('label' => 'Letter', 'value' => 1)
                ),
                array('a4' => $this->translate('A4'), 'letter' => $this->translate('Letter'))
            ),
            array(array(), array()),
        );
    }

   /**
    * test getSizeByName method
    *
    * @param array $configSectionArray
    * @param string $pageFormat
    * @param array $expected
    *
    * @dataProvider testGetSizeByNameProvider
    */
    public function testGetSizeByName($configSectionArray, $pageFormat, $expectedWidth, $expectedHeight)
    {
        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_PageSize')
            ->setMethods(array('_getPageSizeModel', '_getSource', '_getConfigModel'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getSource')
            ->will($this->returnValue(array('A4' => $configSectionArray)));

        $config = array(
            'name' => $configSectionArray['name'],
            'height' => new Zend_Measure_Length($configSectionArray['height'], Zend_Measure_Length::MILLIMETER),
            'width' => new Zend_Measure_Length($configSectionArray['width'], Zend_Measure_Length::MILLIMETER),
        );

        $pageSizeModel = $this->getMock('Saas_PrintedTemplate_Model_PageSize',
            array('getDataByPath'), array($config));

        $model->expects($this->once())
            ->method('_getPageSizeModel')
            ->will($this->returnValue($pageSizeModel));

        $model->getSizeByName($pageFormat);

        $this->assertInstanceOf('Saas_PrintedTemplate_Model_PageSize', $pageSizeModel);
        $this->assertEquals($expectedWidth, $pageSizeModel->getWidth()->getValue());
        $this->assertEquals($expectedHeight, $pageSizeModel->getHeight()->getValue());
    }

   /**
    * Provide test data for testGetSizes mmethod
    *
    * @return array
    */
    public function testGetSizeByNameProvider()
    {
        return array(
            array(
                array(
                    'name' => 'A4', 'value' => 0, 'height' => '100', 'width' => '120'
                ), 'A4', '120', '100'
            )
        );
    }

    /**
    * test getSizeByName method
    *
    * @param array $configSectionArray
    * @param string $pageFormat
    *
    * @dataProvider testGetSizeByNameExceptionProvider
    *
    * @expectedException InvalidArgumentException
    */
    public function testGetSizeByNameException($configSectionArray, $pageFormat)
    {
        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_PageSize')
            ->setMethods(array('_getPageSizeModel', '_getSource', '_getConfigModel'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getSource')
            ->will($this->returnValue(array('A4' => $configSectionArray)));

        $model->getSizeByName($pageFormat);
    }

   /**
    * Provide test data for testGetSizes mmethod
    *
    * @return array
    */
    public function testGetSizeByNameExceptionProvider()
    {
        return array(
            array(
                array(
                    'name' => 'A4', 'value' => 0, 'height' => '100', 'width' => '120'
                ), 'A5'
            )
        );
    }
}
