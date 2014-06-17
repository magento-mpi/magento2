<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Handler\CmsPage;

use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Handler\Conditions;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating Cms page
 */
class Curl extends Conditions implements CmsPageInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Published' => 1,
            'Disabled' => 0
        ],
        'store_id' => [
            'All Store Views' => 0,
        ],
        'root_template' => [
            '1 column' => 'one_column',
            '2 columns with left bar' => 'two_columns_left',
            '2 columns with right bar' => 'two_columns_right',
            '3 columns' => 'three_columns'
        ],
        'under_version_control' => [
            'Yes' => 1,
            'No' => 0
        ]
    ];

    /**
     * Post request for creating a cms page
     *
     * @param FixtureInterface $fixture
     * @return void
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'admin/cms_page/save/';
        $data = $this->replaceMappingData($fixture->getData());
        $data['stores'] = [$data['store_id']];
        unset($data['store_id']);
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Cms page entity creating by curl handler was not successful! Response: $response");
        }
    }
}
