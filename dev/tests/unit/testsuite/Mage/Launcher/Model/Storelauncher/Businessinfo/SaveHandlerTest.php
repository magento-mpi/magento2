<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Businessinfo_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Save function test
     *
     * @dataProvider generateSaveData
     * @param array $data Request data
     * @param array $expectedData
     * @param int $timesToCall
     */
    public function testSave($data, $expectedData, $timesToCall)
    {
        $config = $this->getMock(
            'Mage_Backend_Model_Config',
            array('setSection', 'setGroups', 'save'),
            array(),
            '',
            false
        );

        if(isset($expectedData['shipping'])) {
            $setSectionWith = $this->logicalOr(
                $this->equalTo('general'),
                $this->equalTo('trans_email'),
                $this->equalTo('shipping')
            );

            $setGroupsWith = $this->logicalOr(
                $this->equalTo($expectedData['general']),
                $this->equalTo($expectedData['trans_email']),
                $this->equalTo($expectedData['shipping'])
            );
        } else {
            $setSectionWith = $this->logicalOr(
                $this->equalTo('general'),
                $this->equalTo('trans_email')
            );

            $setGroupsWith = $this->logicalOr(
                $this->equalTo($expectedData['general']),
                $this->equalTo($expectedData['trans_email'])
            );
        }

        $config->expects($this->exactly($timesToCall))
            ->method('setSection')
            ->with($setSectionWith)
            ->will($this->returnValue($config));

        $config->expects($this->exactly($timesToCall))
            ->method('setGroups')
            ->with($setGroupsWith)
            ->will($this->returnValue($config));

        $config->expects($this->exactly($timesToCall))
            ->method('save');

        $regionModel = $this->_getRegionMock();

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Businessinfo_SaveHandler(
             $config,
             $regionModel
        );
        $saveHandler->save($data);
    }

    /**
     * Prepare Address Data for system configuration test
     *
     * @dataProvider generatePrepareData
     * @param array $data
     * @param array $expectedData
     */
    public function testPrepareData($data, $expectedData)
    {
        $configStub = $this->getMock(
            'Mage_Backend_Model_Config',
            array(),
            array(),
            '',
            false
        );

        $regionModel = $this->_getRegionMock();

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Businessinfo_SaveHandler(
             $configStub,
             $regionModel
        );

        $result = $saveHandler->prepareData($data);
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Create Region Mock
     *
     * @return Mage_Directory_Model_Region
     */
    protected function _getRegionMock()
    {
        $regionModel = $this->getMock(
            'Mage_Directory_Model_Region',
            array('load', 'getName'),
            array(),
            '',
            false
        );

        $regionModel->expects($this->once())
            ->method('load')
            ->with($this->equalTo(5))
            ->will($this->returnValue($regionModel));

        $regionModel->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('Alaska'));

        return $regionModel;
    }

    /**
     * Data provider for testPrepareData method
     *
     * @return array
     */
    public function generatePrepareData()
    {
        return array(
            array(
                $this->_getTestData(false),
                $this->_getExpectedData(false)
            ),
            array(
                $this->_getTestData(true),
                $this->_getExpectedData(true)
            ),
        );
    }

    /**
     * Data provider for testSave methods
     *
     * @return array
     */
    public function generateSaveData()
    {
        return array(
            array(
                $this->_getTestData(false),
                $this->_getExpectedData(false),
                2
            ),
            array(
                $this->_getTestData(true),
                $this->_getExpectedData(true),
                3
            ),
        );
    }

    /**
     * Get array of test data, emulating request data
     *
     * @param bool $useShipping
     * @return array
     */
    protected function _getTestData($useShipping = false)
    {
        $result = array(
            'groups' => array(
                'general' => array(
                    'store_information' => array(
                        'fields' => array(
                            'name' => array('value' => 'Store Name 1'),
                            'phone' => array('value' => '123456789'),
                            'merchant_country' => array('value' => 'US'),
                            'merchant_vat_number' => array('value' => '444444444'),
                        ),
                    ),
                ),
                'trans_email' => array(
                    'ident_general' => array(
                        'fields' => array(
                            'email' => array('value' => 'owner123@example.com'),
                        ),
                    ),
                    'ident_sales' => array(
                        'fields' => array(
                            'name' => array('value' => 'Sales'),
                            'email' => array('value' => 'sales@example.com'),
                        ),
                    ),
                    'ident_support' => array(
                        'fields' => array(
                            'name' => array('value' => 'CustomerSupport'),
                            'email' => array('value' => 'support@example.com'),
                        ),
                    ),
                    'ident_custom1' => array(
                        'fields' => array(
                            'name' => array('value' => 'Custom'),
                            'email' => array('value' => 'custom@example.com'),
                        ),
                    ),
                    'ident_custom2' => array(
                        'fields' => array(
                            'name' => array('value' => 'Custom'),
                            'email' => array('value' => 'custom@example.com'),
                        ),
                    ),
                ),
            ),
            'street_line1' => 'Zoologichna',
            'street_line2' => '5 A',
            'city' => 'Kiev',
            'region_id' => 5,
            'postcode' => '01133',
            'tileCode' => 'business_info',
        );

        if ($useShipping) {
            $result['use_for_shipping'] = 1;
        }
        return $result;
    }

    /**
     * Get Expected data
     *
     * @param bool $useShipping
     * @return array
     */
    protected function _getExpectedData($useShipping = false)
    {
        $result = array(
            'general' => array(
                'store_information' => array(
                    'fields' => array(
                        'name' => array('value' => 'Store Name 1'),
                        'phone' => array('value' => '123456789'),
                        'merchant_country' => array('value' => 'US'),
                        'merchant_vat_number' => array('value' => '444444444'),
                        'address' => array(
                            'value' => "Zoologichna\n5 A\nKiev\n01133\nAlaska"
                        ),
                    ),
                ),
            ),
            'trans_email' => array(
                'ident_general' => array(
                    'fields' => array(
                        'email' => array('value' => 'owner123@example.com'),
                    ),
                ),
                'ident_sales' => array(
                    'fields' => array(
                        'name' => array('value' => 'Sales'),
                        'email' => array('value' => 'sales@example.com'),
                    ),
                ),
                'ident_support' => array(
                    'fields' => array(
                        'name' => array('value' => 'CustomerSupport'),
                        'email' => array('value' => 'support@example.com'),
                    ),
                ),
                'ident_custom1' => array(
                    'fields' => array(
                        'name' => array('value' => 'Custom'),
                        'email' => array('value' => 'custom@example.com'),
                    ),
                ),
                'ident_custom2' => array(
                    'fields' => array(
                        'name' => array('value' => 'Custom'),
                    'email' =>
                    array (
                      'value' => 'custom@example.com',
                    ),
                  ),
                ),
            ),
        );

        if ($useShipping) {
            $shipping = array(
                'shipping' => array(
                    'origin' => array(
                        'fields' => array(
                            'country_id' => array('value' => 'US'),
                            'region_id' => array('value' => 5),
                            'postcode' => array('value' => '01133'),
                            'city' => array('value' => 'Kiev'),
                            'street_line1' => array('value' => 'Zoologichna'),
                            'street_line2' => array('value' => '5 A'),
                        ),
                    ),
                )
            );
            $result = array_merge($result, $shipping);
        }
        return $result;
    }
}
