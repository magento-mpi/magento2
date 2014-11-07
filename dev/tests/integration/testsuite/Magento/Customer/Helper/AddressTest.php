<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Helper\Address */
    protected $helper;

    protected function setUp()
    {
        $this->helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Customer\Helper\Address'
        );
    }

    /**
     * @param $attributeCode
     * @dataProvider getAttributeValidationClass
     */
    public function testGetAttributeValidationClass($attributeCode, $expectedClass)
    {
        $this->assertEquals($expectedClass, $this->helper->getAttributeValidationClass($attributeCode));
    }

    public function getAttributeValidationClass()
    {
        return array(
            array('bad-code', ''),
            array('city', ' required-entry'),
            array('company', ''),
            array('country_id', ' required-entry'),
            array('fax', ''),
            array('firstname', 'required-entry'),
            array('lastname', 'required-entry'),
            array('middlename', ''),
            array('postcode', '')
        );
    }
}
