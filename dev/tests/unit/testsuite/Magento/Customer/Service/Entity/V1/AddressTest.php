<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\Entity\V1;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /** Sample values for testing */
    const ID = 14;
    const IS_SHIPPING = true;
    const IS_BILLING = false;
    const COMPANY = 'Company Name';
    const FAX = '(555) 555-5555';
    const MIDDLENAME = 'Mid';
    const PREFIX = 'Mr.';
    const SUFFIX = 'Esq.';
    const VAT_ID = 'S45';
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const STREET_LINE_0 = '7700 W Parmer Ln';
    const CITY = 'Austin';
    const COUNTRY_CODE = 'US';
    const POSTCODE = '78620';
    const TELEPHONE = '5125125125';
    const REGION = 'Texas';

    protected $_expectedValues = [
        'id' => 14,
        'default_shipping' => true,
        'default_billing' => false,
        'company' => 'Company Name',
        'fax' => '(555) 555-5555',
        'middlename' => 'Mid',
        'prefix' => 'Mr.',
        'suffix' => 'Esq.',
        'vat_id' => 'S45',
        'firstname' => 'Jane',
        'lastname' => 'Doe',
        'street' => ['7700 W Parmer Ln'],
        'city' => 'Austin',
        'country_id' => 'US',
        'postcode' => '78620',
        'telephone' => '5125125125',
        'region_id' => 0,
        'region' => 'Texas',
    ];

    public function testMinimalAddress()
    {
        $address = new Address();
        $this->_fillMinimumRequiredFields($address);
        $this->_assertMinimumRequiredFields($address);
    }

    public function testCopyAndModify()
    {
        /** @var \Magento\Customer\Service\Entity\V1\AddressInterface $origAddress */
        $origAddress = $this->getMockBuilder('\Magento\Customer\Service\Entity\V1\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockReturnValue($origAddress, array(
            'getFirstname' => $this->_expectedValues['firstname'],
            'getLastname' => $this->_expectedValues['lastname'],
            'getStreet' => $this->_expectedValues['street'],
            'getCity' => $this->_expectedValues['city'],
            'getCountryId' => $this->_expectedValues['country_id'],
            'getRegion' => new Region('', $this->_expectedValues['region']),
            'getPostcode' => $this->_expectedValues['postcode'],
            'getTelephone' => $this->_expectedValues['telephone'],
        ));

        $this->_assertMinimumRequiredFields($origAddress);
    }

    public function testFullAddress()
    {
        $address = new Address();
        $this->_fillAllFields($address);


        $this->_assertMinimumRequiredFields($address);
        $this->assertEquals($this->_expectedValues['id'], $address->getId());
        $this->assertEquals($this->_expectedValues['default_shipping'], $address->isDefaultShipping());
        $this->assertEquals($this->_expectedValues['default_billing'], $address->isDefaultBilling());
        $this->assertEquals($this->_expectedValues['company'], $address->getCompany());
        $this->assertEquals($this->_expectedValues['fax'], $address->getFax());
        $this->assertEquals($this->_expectedValues['middlename'], $address->getMiddlename());
        $this->assertEquals($this->_expectedValues['prefix'], $address->getPrefix());
        $this->assertEquals($this->_expectedValues['suffix'], $address->getSuffix());
        $this->assertEquals($this->_expectedValues['vat_id'], $address->getVatId());
    }

    public function testSetStreet()
    {
        $address = new Address();
        $this->_fillMinimumRequiredFields($address);
        $street = $address->getStreet();
        $street[] = 'Line_1';
        $address->setStreet($street);
        $this->_assertMinimumRequiredFields($address);
        $this->assertEquals('Line_1', $address->getStreet()[1]);
    }

    public function testGetAttributes()
    {
        $address = new Address();
        $this->_fillAllFields($address);
        $expected = $this->_expectedValues;
        unset($expected['id']);
        unset($expected['default_billing']);
        unset($expected['default_shipping']);
        $this->assertEquals($expected, $address->getAttributes());
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
        }
    }

    /**
     * @param Address $address
     */
    private function _fillMinimumRequiredFields($address)
    {
        $address->setFirstname($this->_expectedValues['firstname']);
        $address->setLastname($this->_expectedValues['lastname']);
        $address->setStreet($this->_expectedValues['street']);
        $address->setCity($this->_expectedValues['city']);
        $address->setCountryId($this->_expectedValues['country_id']);
        $address->setRegion(new Region('', $this->_expectedValues['region']));
        $address->setPostcode($this->_expectedValues['postcode']);
        $address->setTelephone($this->_expectedValues['telephone']);
    }

    /**
     * @param Address $address
     */
    private function _fillAllFields($address)
    {
        $this->_fillMinimumRequiredFields($address);

        $address->setId($this->_expectedValues['id']);
        $address->setSuffix($this->_expectedValues['suffix']);
        $address->setMiddlename($this->_expectedValues['middlename']);
        $address->setPrefix($this->_expectedValues['prefix']);
        $address->setVatId($this->_expectedValues['vat_id']);
        $address->setDefaultShipping($this->_expectedValues['default_shipping']);
        $address->setDefaultBilling($this->_expectedValues['default_billing']);
        $address->setCompany($this->_expectedValues['company']);
        $address->setFax($this->_expectedValues['fax']);
    }

    /**
     * @param Address $address
     */
    private function _assertMinimumRequiredFields($address)
    {
        $this->assertEquals($this->_expectedValues['firstname'], $address->getFirstname());
        $this->assertEquals($this->_expectedValues['lastname'], $address->getLastname());
        $this->assertEquals($this->_expectedValues['street'][0], $address->getStreet()[0]);
        $this->assertEquals($this->_expectedValues['city'], $address->getCity());
        $this->assertEquals($this->_expectedValues['country_id'], $address->getCountryId());
        $this->assertEquals(new Region('', $this->_expectedValues['region']), $address->getRegion());
        $this->assertEquals($this->_expectedValues['postcode'], $address->getPostcode());
        $this->assertEquals($this->_expectedValues['telephone'], $address->getTelephone());
    }
}
