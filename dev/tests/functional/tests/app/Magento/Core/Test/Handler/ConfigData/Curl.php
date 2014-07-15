<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Handler\ConfigData;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Setting config
 */
class Curl extends AbstractCurl implements ConfigDataInterface
{
    /**
     * Post request for setting configuration attribute
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        foreach ($data as $scope => $item) {
            $this->applyConfigSettings($item, $scope);
        }
    }


    /**
     * Prepare POST data for setting configuration attribute
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $result = [];
        $fields = $fixture->getData();
        if (isset($fields['section'])) {
            foreach ($fields['section'] as $itemSection) {
                list($scope, $group, $field) = explode('/', $itemSection['path']);
                $result[$scope]['groups'][$group]['fields'][$field]['value'] = $itemSection['value'];
            }
        }
        return $result;
    }

    /**
     * Apply config settings via curl
     *
     * @param array $data
     * @param string $section
     * @return int|null
     * @throws \Exception
     */
    protected function applyConfigSettings(array $data, $section)
    {
        $url = $this->getUrl($section);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (strpos($response, 'data-ui-id="messages-message-success"') === false) {
            throw new \Exception("Settings are not applied! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }

    /**
     * Retrieve URL for request
     *
     * @param string $section
     * @return string
     */
    protected function getUrl($section)
    {
        return $_ENV['app_backend_url'] . 'admin/system_config_save/index/section/' . $section;
    }
}
