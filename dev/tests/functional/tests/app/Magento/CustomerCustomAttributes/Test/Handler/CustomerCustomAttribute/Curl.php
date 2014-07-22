<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Handler\CustomerCustomAttribute;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating custom CustomerAttribute
 */
class Curl extends AbstractCurl implements CustomerCustomAttributeInterface
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'admin/customer_attribute/save/active_tab/general';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'frontend_input' => [
            'Text Field' => 'text',
            'Text Area' => 'textarea',
            'Multiple Line' => 'multiline',
            'Date' => 'date',
            'Dropdown' => 'select',
            'Multiple Select' => 'multiselect',
            'Yes/No' => 'boolean',
            'File (attachment)' => 'file',
            'Image File' => 'image',
        ],
    ];

    /**
     * POST request for creating Custom CustomerAttribute
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl;
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("CustomerAttribute creating by curl handler was not successful! Response: $response");
        }

        return ['attribute_id' => $this->getAttributeId($fixture->getAttributeCode())];
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
        $frontendLabels[] = $data['frontend_label'];
        if (isset($data['manage_title'])) {
            $frontendLabels[] = $data['manage_title'];
        }
        $data['frontend_label'] = $frontendLabels;

        return $data;
    }

    /**
     * Get CustomerAttribute id by attribute code
     *
     * @param string $attributeCode
     * @return int|null
     */
    protected function getAttributeId($attributeCode)
    {
        $filter = ['attribute_code' => $attributeCode];
        $url = $_ENV['app_backend_url'] . 'admin/customer_attribute/index/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();
        preg_match('`\/attribute_id\/(\d*?)\/`', $response, $match);

        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Encoded filter parameters
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }
}
