<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Webapi\Controller\Rest\Response;

/**
 * Unit test for PartialResponseProcessor
 */
class PartialResponseProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PartialResponseProcessor SUT
     */
    protected $processor;

    /**
     * @var string
     */
    protected $sampleResponseValue;

    /**
     * Setup SUT
     */
    public function setUp()
    {
        $this->processor = new PartialResponseProcessor();
        $this->sampleResponseValue = array(
            'customer' =>
                array(
                    'id' => '1',
                    'website_id' => '0',
                    'created_in' => 'Default Store View',
                    'store_id' => '1',
                    'group_id' => '1',
                    'custom_attributes' =>
                        array(
                            0 =>
                                array(
                                    'attribute_code' => 'disable_auto_group_change',
                                    'value' => '0',
                                ),
                        ),
                    'firstname' => 'Jane',
                    'lastname' => 'Doe',
                    'email' => 'jdoe@ebay.com',
                    'default_billing' => '1',
                    'default_shipping' => '1',
                    'created_at' => '2014-05-27 18:59:43',
                    'dob' => '1983-05-26 00:00:00',
                    'taxvat' => '1212121212',
                    'gender' => '1',
                ),
            'addresses' =>
                array(
                    0 =>
                        array(
                            'firstname' => 'Jane',
                            'lastname' => 'Doe',
                            'street' =>
                                array(
                                    0 => '7700  Parmer ln',
                                ),
                            'city' => 'Austin',
                            'country_id' => 'US',
                            'region' =>
                                array(
                                    'region' => 'Texas',
                                    'region_id' => 57,
                                    'region_code' => 'TX',
                                ),
                            'postcode' => '78728',
                            'telephone' => '1111111111',
                            'default_billing' => true,
                            'default_shipping' => true,
                            'id' => '1',
                            'customer_id' => '1',
                        ),
                    1 =>
                        array(
                            'firstname' => 'Jane',
                            'lastname' => 'Doe',
                            'street' =>
                                array(
                                    0 => '2211 N First St ',
                                ),
                            'city' => 'San Jose',
                            'country_id' => 'US',
                            'region' =>
                                array(
                                    'region' => 'California',
                                    'region_id' => 23,
                                    'region_code' => 'CA',
                                ),
                            'postcode' => '98454',
                            'telephone' => '2222222222',
                            'default_billing' => true,
                            'default_shipping' => true,
                            'id' => '2',
                            'customer_id' => '1',
                        ),
                ),
        );
    }

    public function testFilterNoNesting()
    {
        $expected = array('customer' => $this->sampleResponseValue['customer']);

        $simpleFilter = 'customer';

        $filteredResponse = $this->processor->filter(
            $simpleFilter,
            $this->sampleResponseValue
        );

        $this->assertEquals($expected, $filteredResponse);
    }

    public function testFilterSimpleNesting()
    {
        $expected = array(
            'customer' => [
                'email' => $this->sampleResponseValue['customer']['email'],
                'id' => $this->sampleResponseValue['customer']['id']
            ]
        );

        $simpleFilter = "customer[email,id]";

        $filteredResponse = $this->processor->filter(
            $simpleFilter,
            $this->sampleResponseValue
        );

        $this->assertEquals($expected, $filteredResponse);
    }

    public function testFilterMultilevelNesting()
    {
        $expected = array(
            'customer' =>
                array(
                    'id' => '1',
                    'email' => 'jdoe@ebay.com',
                ),
            'addresses' =>
                array(
                    0 =>
                        array(
                            'city' => 'Austin',
                            'region' =>
                                array(
                                    'region' => 'Texas',
                                    'region_code' => 'TX',
                                ),
                            'postcode' => '78728',
                        ),
                    1 =>
                        array(
                            'city' => 'San Jose',
                            'region' =>
                                array(
                                    'region' => 'California',
                                    'region_code' => 'CA',
                                ),
                            'postcode' => '98454',
                        ),
                ),
        );

        $nestedFilter = 'customer[id,email],addresses[city,postcode,region[region_code,region]]';

        $filteredResponse = $this->processor->filter(
            $nestedFilter,
            $this->sampleResponseValue
        );

        $this->assertEquals($expected, $filteredResponse);
    }

    public function testNonExistentFieldFilter()
    {
        //TODO : Make sure if this behavior is acceptable
        $expected = array(
            'customer' =>
                array(
                    'id' => '1',
                    'email' => 'jdoe@ebay.com',
                ),
            'addresses' =>
                array(
                    0 =>
                        array(
                            //'city' => 'Austin', //City has been substituted with 'invalid' field
                            'region' =>
                                array(
                                    'region' => 'Texas',
                                    'region_code' => 'TX',
                                ),
                            'postcode' => '78728',
                        ),
                    1 =>
                        array(
                            //'city' => 'San Jose',
                            'region' =>
                                array(
                                    'region' => 'California',
                                    'region_code' => 'CA',
                                ),
                            'postcode' => '98454',
                        ),
                ),
        );

        $nonExistentFieldFilter = 'customer[id,email],addresses[invalid,postcode,region[region_code,region]]';

        $filteredResponse = $this->processor->filter(
            $nonExistentFieldFilter,
            $this->sampleResponseValue
        );

        $this->assertEquals($expected, $filteredResponse);
    }

    /**
     * @dataProvider invalidFilterDataProvider
     */
    public function testInvalidFilters($invalidFilter)
    {
        $filteredResponse = $this->processor->filter(
            $invalidFilter,
            $this->sampleResponseValue
        );

        $this->assertEmpty($filteredResponse);
    }

    /**
     * Data provider for invalid Filters
     *
     * @return array
     */
    public function invalidFilterDataProvider()
    {
        return [
            ['  '],
            [null],
            ['customer(email)'],
            [' customer[email]'],
            ['-'],
            ['customer[id,email],addresses[city,postcode,region[region_code,region]'] //Missing last parentheses
        ];
    }

    public function testExtractFilter()
    {
        $request = new \Zend_Controller_Request_Http();
        $sampleFilter = 'customer[id,email],addresses[city,postcode,region[region_code,region]]';
        $params = ['fields' => $sampleFilter, 'id' => 1];
        $request->setParams($params);
        $this->assertEquals($sampleFilter, $this->processor->extractFilter($request));
    }

    public function testExtractFilterNoFilterProvided()
    {
        $request = new \Zend_Controller_Request_Http();
        $params = ['id' => 1];
        $request->setParams($params);
        $this->assertNull($this->processor->extractFilter($request));
    }
}
 