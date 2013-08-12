<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage unit_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Saas_PrintedTemplate_Model_Variable_Address_AbstractTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test Get Country method
     *
     * @param int $countryId
     * @param string $countryName
     * @string string $expectedCountry
     *
     * @dataProvider getCountryProvider
     */
    public function testGetCountry($countryId, $countryData, $expectedCountry)
    {
        $country  = new Magento_Object();
        foreach ($countryData as $field => $value) {
            $country->setData($field, $value);
        }

        $countryModel = $this->getMockBuilder('Magento_Directory_Model_Country')
            ->disableOriginalConstructor()
            ->setMethods(array('load'))
            ->getMock();

        $countryModel->expects($this->once())
            ->method('load')
            ->with($this->equalTo($countryId))
            ->will($this->returnValue($country));

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Address_Abstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_getCountryModel', '_setListsFromConfig'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_setListsFromConfig');

        $entity->expects($this->any())
            ->method('_getCountryModel')
            ->will($this->returnValue($countryModel));

        $valueModel = $this->getMockBuilder('Magento_Sales_Model_Order_Address')
            ->disableOriginalConstructor()
            ->setMethods(array('getCountryId'))
            ->getMock();

        $valueModel->expects($this->once())
            ->method('getCountryId')
            ->will($this->returnValue($countryId));

        $entity->__construct($valueModel);

        $actualCountry = $entity->getCountry();
        $this->assertEquals($expectedCountry, $actualCountry);
    }

    /**
     * Get country test data provider
     *
     * @return array
     */
    public function getCountryProvider()
    {
        return array(
            array(null, array('id' => 1, 'name' => 'Albania'), 'Albania'),
            array(1, array('id' => 1, 'name' => 'Albania'), 'Albania'),
            array(1, array(), 1)
        );
    }

    /**
     * Test Get Street method
     *
     * @param string $street
     * @string string $expectedStreet
     *
     * @dataProvider getStreetProvider
     */
    public function testGetStreet($street, $expectedStreet)
    {
        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Address_Abstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_setListsFromConfig'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_setListsFromConfig');

        $valueModel = $this->getMockBuilder('Magento_Sales_Model_Order_Address')
            ->disableOriginalConstructor()
            ->setMethods(array('getStreet'))
            ->getMock();

        $valueModel->expects($this->once())
            ->method('getStreet')
            ->will($this->returnValue($street));

        $entity->__construct($valueModel);

        $actualStreet = $entity->getStreet();
        $this->assertEquals($expectedStreet, $actualStreet);
    }

    public function getStreetProvider()
    {
        return array(
            array(array(), ''),
            array(array('str1', 'str2'), 'str1; str2'),
            array(array(1, 2, 3), '1; 2; 3')
        );
    }

}
