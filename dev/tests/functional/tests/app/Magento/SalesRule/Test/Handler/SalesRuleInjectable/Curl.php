<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Handler\SalesRuleInjectable;

use Magento\SalesRule\Test\Handler\SalesRuleInjectable;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Handler\Conditions;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 */
class Curl extends Conditions implements SalesRuleInjectableInterface
{
    /**
     * Prepare data for updating sales rule
     *
     * @param FixtureInterface $fixture
     * @return void
     * @throws \Exception
     */
   public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'sales_rule/promo_quote/save/';
        $data = $this->conversionDataForCurl($fixture->getData());
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Sales rule entity creating by curl handler was not successful! Response: $response");
        }
    }

    /**
     * Conversion data for curl
     *
     * @param $uiData
     * @return array
     */
    protected function conversionDataForCurl($uiData)
    {
        $curlData = [];
        foreach($uiData as $key => $value){
            switch ($key) {
                case 'is_active':
                    $curlData['is_active'] = ($value == 'Active') ? '1' : '0';
                    break;
                case 'customer_group_ids':
                    foreach($uiData['customer_group_ids'] as $groupKey => $groupName){
                        $curlData['customer_group_ids'][] = $groupKey;
                    }
                    break;
                case 'website_ids':
                    foreach($uiData['website_ids'] as $websiteKey => $websiteName){
                        $curlData['website_ids'][] = $websiteKey;
                    }
                    break;
                case 'coupon_type':
                   switch($value){
                       case 'No coupon':
                           $curlData['coupon_type'] = '1';
                           break;
                       case 'Specific Coupon':
                           $curlData['coupon_type'] = '2';
                           break;
                       case 'Auto':
                           $curlData['coupon_type'] = '3';
                           break;
                   }
                    break;
                case 'is_rss':
                    $curlData['is_rss'] = ($value == 'Yes') ? '1' : '0';
                    break;
                case 'rule':
                    $rule = [];
                    foreach ($value as $type => $val) {
                        $rule[$type] = $this->prepareCondition($val);
                    }
                    $curlData['rule'] = $rule;
                    break;
                case 'simple_action':
                    switch($value){
                        case 'Percent of product price discount':
                            $curlData['simple_action'] = 'by_percent';
                            break;
                        case 'Fixed amount discount':
                            $curlData['simple_action'] = 'by_fixed';
                            break;
                        case 'Fixed amount discount for whole cart':
                            $curlData['simple_action'] = 'cart_fixed';
                            break;
                        case 'Buy X get Y free (discount amount is Y)':
                            $curlData['simple_action'] = 'buy_x_get_y';
                            break;
                    }
                    break;
                case 'apply_to_shipping':
                    $curlData['apply_to_shipping'] = ($value == 'Yes') ? '1' : '0';
                    break;
                case 'stop_rules_processing':
                    $curlData['stop_rules_processing'] = ($value == 'Yes') ? '1' : '0';
                    break;
                case 'simple_free_shipping':
                    switch($value){
                        case 'No':
                            $curlData['simple_free_shipping'] = '0';
                            break;
                        case 'For matching items only':
                            $curlData['simple_free_shipping'] = '1';
                            break;
                        case 'For shipment with matching items':
                            $curlData['simple_free_shipping'] = '2';
                            break;
                    }
                    break;
                default:
                    $curlData[$key] = $value;
            }
        }

        return $curlData;
    }
}
