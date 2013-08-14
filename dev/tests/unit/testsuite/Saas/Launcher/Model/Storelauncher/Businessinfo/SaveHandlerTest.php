<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Businessinfo_SaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Magento_Core_Model_Config $config,
        Magento_Backend_Model_Config $backendConfigModel
    ) {
        return new Saas_Launcher_Model_Storelauncher_Businessinfo_SaveHandler(
            $config,
            $backendConfigModel
        );
    }

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    public function prepareDataValidInputDataProvider()
    {
        return array(
            array(
                $this->_getTestData(false),
                $this->_getExpectedData(false),
                array('general', 'trans_email'),
            ),
            array(
                $this->_getTestData(true),
                $this->_getExpectedData(true),
                array('general', 'trans_email', 'shipping'),
            ),
        );
    }

    /**
     * This data provider emulates invalid input for prepareData method
     *
     * @return array
     */
    public function prepareDataInvalidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['general']['trans_email']['ident_general']['fields']['email']['value'] = 'wrong_email';

        $data1 = array();

        return array(
            array($data0),
            array($data1),
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
                            'country_id' => array('value' => 'US'),
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
                        'country_id' => array('value' => 'US'),
                        'country_id' => array('value' => 'US'),
                        'region_id' => array('value' => 5),
                        'postcode' => array('value' => '01133'),
                        'city' => array('value' => 'Kiev'),
                        'street_line1' => array('value' => 'Zoologichna'),
                        'street_line2' => array('value' => '5 A'),
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
                        'email' => array(
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
