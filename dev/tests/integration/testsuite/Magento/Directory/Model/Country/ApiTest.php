<?php
/**
 * Directory country API test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Directory_Model_Country_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Test 'items' method of directory country API.
     */
    public function testList()
    {
        $countries = Magento_TestFramework_Helper_Api::call($this, 'directoryCountryList');
        $this->assertGreaterThan(200, count($countries), "The list of countries seems to be not full.");
        $countryData = reset($countries);
        $expectedFields = array('country_id', 'iso2_code', 'iso3_code', 'name');
        $missingFields = array_diff($expectedFields, array_keys($countryData));
        $this->assertEmpty(
            $missingFields,
            sprintf("The following fields must be present in country data: %s.", implode(', ', $missingFields))
        );
    }
}
