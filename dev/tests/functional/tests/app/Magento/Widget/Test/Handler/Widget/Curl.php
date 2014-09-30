<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Handler\Widget;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for creating widgetInstance/frontendApp
 */
class Curl extends AbstractCurl
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'theme_id' => [
            'Magento Blank' => 2,
        ],
        'code' => [
            'CMS Page Link' => 'cms_page_link',
        ],
    ];

    /**
     * Mapping store ids values for data.
     *
     * @var array
     */
    protected $mappingStoreIds = [
        'All Store Views' => 0
    ];

    /**
     * Post request for creating widget instance
     *
     * @param FixtureInterface $fixture [optional]
     * @throws \Exception
     * @return null|array instance id
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'admin/widget_instance/save/code/'
            . $data['code'] . '/theme_id/' . $data['theme_id'];
        if (isset($data['page_id'])) {
            $data['parameters']['page_id'] = $data['page_id'][0];
            unset($data['page_id']);
        }
        if ($fixture->hasData('store_ids')) {
            $data['store_ids'][0] = $fixture->getDataFieldConfig('store_ids')['source']->getStore()[0]->getStoreId();
        }
        unset($data['code']);
        unset($data['theme_id']);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Widget instance creation by curl handler was not successful! Response: $response");
        }
        $id = null;
        if (preg_match_all('/\/widget_instance\/edit\/instance_id\/(\d+)/', $response, $matches)) {
            $id = $matches[1][count($matches[1]) - 1];
        }
        return ['id' => $id];
    }

    /**
     * Prepare data for create widget
     *
     * @param FixtureInterface $widget
     * @return array
     */
    protected function prepareData(FixtureInterface $widget)
    {
        $data = $this->replaceMappingData($widget->getData());
        $data = $this->replaceStoreIds($data);

        foreach ($data['widget_instance'] as $key => $widgetInstance) {
            $pageGroup = $widgetInstance['page_group'];

            if (!isset($widgetInstance[$pageGroup]['page_id'])) {
                $widgetInstance[$pageGroup]['page_id'] = 0;
            }
            if ('notanchor_categories' == $pageGroup) {
                $widgetInstance[$pageGroup]['is_anchor_only'] = 0;
            }

            $data['widget_instance'][$key] = $widgetInstance;
        }

        return $data;
    }

    /**
     * Replace store ids labels to values
     *
     * @param array $data
     * @return array
     */
    protected function replaceStoreIds(array $data)
    {
        if (isset($data['store_ids'])) {
            foreach ($data['store_ids'] as $key => $storeId) {
                if (isset($this->mappingStoreIds[$storeId])) {
                    $data['store_ids'][$key] = $this->mappingStoreIds[$storeId];
                }
            }
        }

        return $data;
    }
}
