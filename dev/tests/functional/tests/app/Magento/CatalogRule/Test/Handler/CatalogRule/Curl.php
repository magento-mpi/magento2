<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Handler\CatalogRule;

use Magento\Backend\Test\Handler\Conditions;
use Magento\CatalogRule\Test\Handler\CatalogRule;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl that creates catalog price rule
 */
class Curl extends Conditions implements CatalogRuleInterface
{
    /**
     * Map of type parameter
     *
     * @var array
     */
    protected $mapTypeParams = [
        'Category' => [
            'type' => 'Magento\CatalogRule\Model\Rule\Condition\Product',
            'attribute' => 'category_ids',
        ]
    ];

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'simple_action' => [
            'By Percentage of the Original Price' => 'by_percent',
            'By Fixed Amount' => 'by_fixed',
            'To Percentage of the Original Price' => 'to_percent',
            'To Fixed Amount' => 'to_fixed'
        ],
        'customer_group_ids' => [
            'NOT LOGGED IN' => 0,
            'General' => 1,
            'Wholesale' => 2,
            'Retailer' => 3
        ],
        'is_active' => [
            'Active' => 1,
            'Inactive' => 0
        ],
        'website_ids' => [
            'Main Website' => 1
        ]
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
            throw new \Exception(
                "Catalog Price Rule entity creating by curl handler was not successful! Response: $response"
            );
        }

        return ['id' => $this->getCategoryPriceRuleId($data)];
    }

    /**
     * Prepare data from text to values
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        $data['rule'] = ['conditions' => $this->prepareCondition($data['rule'])];

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
