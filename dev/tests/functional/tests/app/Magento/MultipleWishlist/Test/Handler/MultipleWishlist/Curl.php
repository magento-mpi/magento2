<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Handler\MultipleWishlist;

use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\FrontendDecorator;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class Curl
 * Create new multiple wish list via curl
 */
class Curl extends AbstractCurl implements MultipleWishlistInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'visibility' => [
            'Yes' => 'on',
            'No' => 'off'
        ],
    ];

    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Post request for creating multiple wish list
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();
        $data = $this->replaceMappingData($this->prepareData($fixture));
        return ['id' => $this->createWishlist($data)];
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $data = $fixture->getData();
        unset($data['customer_id']);
        return $data;
    }

    /**
     * Create product via curl
     *
     * @param array $data
     * @return int|null
     * @throws \Exception
     */
    protected function createWishlist(array $data)
    {
        $url = $_ENV['app_frontend_url'] . 'wishlist/index/createwishlist/';
        $curl = new FrontendDecorator(new CurlTransport(), $this->customer);
        $curl->write(CurlInterface::POST, $_ENV['app_frontend_url'] . 'wishlist');
        $curl->read();
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();

        if (strpos($response, 'data-ui-id="global-messages-message-success"') === false) {
            throw new \Exception("Multiple Wish list creation by curl handler was not successful! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }
}
