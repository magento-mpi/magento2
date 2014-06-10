<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Handler\CatalogRule; 

use Magento\CatalogRule\Test\Handler\CatalogRule;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl that creates catalog price rule
 */
class Curl extends AbstractCurl implements CatalogRuleInterface
{
    /**
     * Mapping for simple action
     *
     * @var array
     */
    protected $simpleAction = [
        'by_percent' => 'By Percentage of the Original Price',
        'by_fixed' => 'By Fixed Amount',
        'to_percent' => 'To Percentage of the Original Price',
        'to_fixed' => 'To Fixed Amount'
    ];

    /**
     * Mapping for customer group ids
     *
     * @var array
     */
    protected $customerGroupIds = [
        0 => 'NOT LOGGED IN',
        1 => 'General',
        2 => 'Wholesale',
        3 => 'Retailer'
    ];

    /**
     * POST request for creating Catalog Price Rule
     *
     * @param FixtureInterface $fixture
     * @return mixed|void
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'catalog_rule/promo_catalog/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Catalog Price Rule entity creating by curl handler"
                . " was not successful! Response: $response");
        }

        return ['id' => $this->getCategoryPriceRuleId($data)];
    }

    /**
     * Convert data from text to values
     *
     * @param FixtureInterface $fixture
     * @return mixed
     */
    protected function prepareData($fixture)
    {
        $data = $fixture->getData();
        foreach ($data['website_ids'] as &$value) {
            if ($value == 'Main Website') {
                $value = 1;
            }
        }

        $data['is_active'] = 'Active' ? 1 : 0;

        foreach ($data['customer_group_ids'] as &$value) {
            $value = array_search($value, $this->customerGroupIds);
        }

        $data['simple_action'] = array_search($data['simple_action'], $this->simpleAction);

        $data['limit'] = 20;
        $data['page'] = 1;
        $data['rule']['conditions'][1]['aggregator'] = 'all';
        $data['rule']['conditions'][1]['value']= 1;

        return $data;
    }

    /**
     * Get id after creating Category Price Rule
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function getCategoryPriceRuleId(array $data)
    {
        //Sort data in grid to define category price rule id if more than 20 items in grid
        $url = $_ENV['app_backend_url'] . 'catalog_rule/promo_catalog/index/sort/rule_id/dir/desc';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        $pattern = '/class=\" col\-id col\-rule_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
            . $data['name'] . '/siu';
        preg_match($pattern, $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find Category Price Rule id');
        }
        return $matches[1];
    }
}
