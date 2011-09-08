<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Paas_Helper extends Mage_Selenium_TestCase
{

    public function sync()
    {
        set_time_limit(0);
        // Installation of source Magento instance where orders should be exported from
        $sourceMagentoInstance = array(
            'api_url' => 'http://builds.kpas.varien.com/ws-current/api/v2_soap/?wsdl=1',
            'api_user' => 'api_user',
            'api_key' => '123123q',
        );
        // Installation of target Magento instance where orders should be imported to
        $targetMagentoInstance = array(
            'api_url' => 'https://taf.qs.varien.com/api/v2_soap/?wsdl=1',
            'api_user' => 'admin',
            'api_key' => '123123q',
        );
        // Imported orders increment id prefix
        $prefix = 'imported';
        // Force adding prefix to imported orders increment_id
        $forcePrefix = true;
        // Show detailed info on each imported order
        $detailedOutput = true;
        // Filters
        $filterObj = new stdClass();
        // Simple filter state = new
        $filterObj->filter[] = (object) array(
                    'key' => 'state',
                    'value' => 'new'
        );
        // Complex filters: order increment_id from 100000001 to 100000100
        $filterObj->complex_filter = array();
        $filterObj->complex_filter[] = (object) array(
                    'key' => 'increment_id',
                    'value' => (object) array(
                        'key' => 'from',
                        'value' => '100000001'
                    )
        );
        $filterObj->complex_filter[] = (object) array(
                    'key' => 'increment_id',
                    'value' => (object) array(
                        'key' => 'to',
                        'value' => '100000100'
                    )
        );

        try {
            $sourceApi = new SoapClient($sourceMagentoInstance['api_url']);
            $sourceSessionId = $sourceApi->login(
                    $sourceMagentoInstance['api_user'], $sourceMagentoInstance['api_key']
            );

            $orders = $sourceApi->salesOrderExport($sourceSessionId, $filterObj);

            $this->assertTrue(count($orders), 'Empty array with Orders');

            if (!empty($orders)) {
                $targetApi = new SoapClient($targetMagentoInstance['api_url']);
                $targetSessionId = $targetApi->login(
                        $targetMagentoInstance['api_user'], $targetMagentoInstance['api_key']
                );

                $importResult = $targetApi->salesOrderImport($targetSessionId, $orders, $prefix);
                if ($detailedOutput) {
                    if (count($importResult->result)) {
                        foreach ($importResult->result as $result) {
                            if (isset($result->error)) {

                            } else {
                                if (isset($result->warnings) && !empty($result->warnings)) {
                                    foreach ($result->warnings as $warning) {

                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {

        }
    }

}
