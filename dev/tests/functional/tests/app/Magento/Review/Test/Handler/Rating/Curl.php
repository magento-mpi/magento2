<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Handler\Rating;

use Magento\Backend\Test\Handler\Extractor;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Util\Protocol\CurlTransport;

/**
 * Class Curl
 * Curl handler for creating product Rating through backend.
 */
class Curl extends AbstractCurl implements RatingInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'stores' => [
            'Main Website/Main Website Store/Default Store View' => 1
        ]
    ];

    /**
     * Post request for creating product Rating in backend
     *
     * @param FixtureInterface|null $rating
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $rating = null)
    {
        $url = $_ENV['app_backend_url'] . 'review/rating/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $data = $this->replaceMappingData($rating->getData());

        $data['stores'] = is_array($data['stores']) ? $data['stores'] : [$data['stores']];
        $data += $this->getAdditionalData();
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception(
                'Product Rating entity creating by curl handler was not successful! Response:' . $response
            );
        }

        return ['id' => $this->getProductRatingId()];
    }

    /**
     * Get product Rating id
     *
     * @return int|null
     */
    protected function getProductRatingId()
    {
        $url = 'review/rating/index/sort/rating_id/dir/desc/';
        $regex = '/data-column="rating_id"[^>]*>\s*([0-9]+)\s*</';
        $extractor = new Extractor($url, $regex);
        $match = $extractor->getData();

        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Return additional data for curl request
     *
     * @return array
     */
    protected function getAdditionalData()
    {
        return [
            'rating_codes' => [1 => ''],
            'option_title' => [
                'add_1' => 1,
                'add_2' => 2,
                'add_3' => 3,
                'add_4' => 4,
                'add_5' => 5,
            ],
        ];
    }
}
