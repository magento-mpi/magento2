<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Address;

class AbstractAddressTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject  */
    protected $contextMock;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject  */
    protected $registryMock;

    /** @var \Magento\Directory\Helper\Data|\PHPUnit_Framework_MockObject_MockObject  */
    protected $directoryDataMock;

    /** @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject  */
    protected $eavConfigMock;

    /** @var \Magento\Customer\Model\Address\Config|\PHPUnit_Framework_MockObject_MockObject  */
    protected $addressConfigMock;

    /** @var \Magento\Directory\Model\RegionFactory|\PHPUnit_Framework_MockObject_MockObject  */
    protected $regionFactoryMock;

    /** @var \Magento\Directory\Model\CountryFactory|\PHPUnit_Framework_MockObject_MockObject  */
    protected $countryFactoryMock;

    /** @var \Magento\Customer\Model\Resource\Customer|\PHPUnit_Framework_MockObject_MockObject  */
    protected $resourceMock;

    /** @var \Magento\Framework\Data\Collection\Db|\PHPUnit_Framework_MockObject_MockObject  */
    protected $resourceCollectionMock;

    /** @var \Magento\Customer\Model\Address\AbstractAddress  */
    protected $model;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\Model\Context', array(), array(), '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $this->directoryDataMock = $this->getMock('Magento\Directory\Helper\Data', array(), array(), '', false);
        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);
        $this->addressConfigMock = $this->getMock('Magento\Customer\Model\Address\Config', array(), array(), '', false);
        $this->regionFactoryMock = $this->getMock(
            'Magento\Directory\Model\RegionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->countryFactoryMock = $this->getMock(
            'Magento\Directory\Model\CountryFactory',
            array('create'),
            array(),
            '',
            false
        );
        $regionCollectionMock = $this->getMock(
            'Magento\Directory\Model\Resource\Region\Collection',
            array(),
            array(),
            '',
            false
        );
        $regionCollectionMock->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(0));
        $countryMock = $this->getMock('Magento\Directory\Model\Country', array(), array(), '', false);
        $countryMock->expects($this->any())
            ->method('getRegionCollection')
            ->will($this->returnValue($regionCollectionMock));
        $this->countryFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($countryMock));

        $this->resourceMock = $this->getMock('Magento\Customer\Model\Resource\Customer', array(), array(), '', false);
        $this->resourceCollectionMock = $this->getMock(
            'Magento\Framework\Data\Collection\Db',
            array(),
            array(),
            '',
            false
        );
        $this->model = $this->getMockForAbstractClass(
            'Magento\Customer\Model\Address\AbstractAddress',
            array(
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'directoryData' => $this->directoryDataMock,
                'eavConfig' => $this->eavConfigMock,
                'addressConfig' => $this->addressConfigMock,
                'regionFactory' => $this->regionFactoryMock,
                'countryFactory' => $this->countryFactoryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ),
            '',
            true,
            false

        );
    }

    public function testGetRegionWithRegionId()
    {
        $countryId = 1;
        $this->prepareGetRegion($countryId);

        $this->model->setData(array(
                'region_id' => 1,
                'region' => '',
                'country_id' => $countryId,
            ));
        $this->assertEquals('RegionName', $this->model->getRegion());
    }

    public function testGetRegionWithRegion()
    {
        $countryId = 2;
        $this->prepareGetRegion($countryId);

        $this->model->setData(array(
                'region_id' => '',
                'region' => 2,
                'country_id' => $countryId,
            ));
        $this->assertEquals('RegionName', $this->model->getRegion());
    }

    public function testGetRegionWithRegionName()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->model->setData(array(
                'region_id' => '',
                'region' => 'RegionName',
            ));
        $this->assertEquals('RegionName', $this->model->getRegion());
    }

    public function testGetRegionWithoutRegion()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->assertNull($this->model->getRegion());
    }

    public function testGetRegionCodeWithRegionId()
    {
        $countryId = 1;
        $this->prepareGetRegionCode($countryId);

        $this->model->setData(array(
                'region_id' => 3,
                'region' => '',
                'country_id' => $countryId,
            ));
        $this->assertEquals('UK', $this->model->getRegionCode());
    }

    public function testGetRegionCodeWithRegion()
    {
        $countryId = 2;
        $this->prepareGetRegionCode($countryId);

        $this->model->setData(array(
                'region_id' => '',
                'region' => 4,
                'country_id' => $countryId,
            ));
        $this->assertEquals('UK', $this->model->getRegionCode());
    }

    public function testGetRegionCodeWithRegionName()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->model->setData(array(
                'region_id' => '',
                'region' => 'UK',
            ));
        $this->assertEquals('UK', $this->model->getRegionCode());
    }

    public function testGetRegionCodeWithoutRegion()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->assertNull($this->model->getRegionCode());
    }

    /**
     * @param $countryId
     */
    protected function prepareGetRegion($countryId, $regionName = 'RegionName')
    {
        $region = $this->getMock(
            'Magento\Directory\Model\Region',
            array('getCountryId', 'getName', '__wakeup', 'load'),
            array(),
            '',
            false
        );
        $region->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($regionName));
        $region->expects($this->once())
            ->method('getCountryId')
            ->will($this->returnValue($countryId));
        $this->regionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($region));
    }

    /**
     * @param $countryId
     */
    protected function prepareGetRegionCode($countryId, $regionCode = 'UK')
    {
        $region = $this->getMock(
            'Magento\Directory\Model\Region',
            array('getCountryId', 'getCode', '__wakeup', 'load'),
            array(),
            '',
            false
        );
        $region->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($regionCode));
        $region->expects($this->once())
            ->method('getCountryId')
            ->will($this->returnValue($countryId));
        $this->regionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($region));
    }

    /**
     * @param $data
     * @param $expected
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate($data, $expected)
    {
        $this->directoryDataMock->expects($this->once())
            ->method('getCountriesWithOptionalZip')
            ->will($this->returnValue(array()));

        $this->directoryDataMock->expects($this->never())
            ->method('isRegionRequired');

        $this->model->setData($data);

        $this->assertEquals($expected, $this->model->validate());
    }

    /**
     * @return array
     */
    public function validateDataProvider()
    {
        $data = array(
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'street' => "Street 1\nStreet 2",
            'city' => 'Odessa',
            'telephone' => '555-55-55',
            'country_id' => 1,
            'postcode' => 07201,
            'region_id' => 1,
        );
        return array(
            array(array_diff_key($data, array('firstname' => '')), array('Please enter the first name.')),
            array(array_diff_key($data, array('lastname' => '')), array('Please enter the last name.')),
            array(array_diff_key($data, array('street' => '')), array('Please enter the street.')),
            array(array_diff_key($data, array('city' => '')), array('Please enter the city.')),
            array(array_diff_key($data, array('telephone' => '')), array('Please enter the telephone number.')),
            array(array_diff_key($data, array('postcode' => '')), array('Gender is required.')),
            array(array_diff_key($data, array('region_id' => '')), array('Gender is required.')),
            array(array_diff_key($data, array('country_id' => '')), array('Please enter the country.')),
            array($data, true),
        );
    }
}
