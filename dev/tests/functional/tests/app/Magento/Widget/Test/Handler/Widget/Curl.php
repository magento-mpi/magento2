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
 * Curl handler for creating widgetInstance/frontendApp.
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
        'block' => [
            'Main Content Area' => 'content',
            'Sidebar Additional' => 'sidebar.additional',
            'Sidebar Main' => 'sidebar.main'
        ],
        'page_group' => [
            'All Pages' => 'all_pages',
            'Specified Page' => 'pages',
            'Page Layouts' => 'page_layouts'
        ],
        'template' => [
            'CMS Page Link Block Template' => 'widget/link/link_block.phtml'
        ],
    ];

    /**
     * Widget Instance Template.
     *
     * @var string
     */
    protected $widgetInstanceTemplate = '';

    /**
     * Post request for creating widget instance.
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
     * Prepare data for create widget.
     *
     * @param FixtureInterface $widget
     * @return array
     */
    protected function prepareData(FixtureInterface $widget)
    {
        $data = $this->replaceMappingData($widget->getData());

        return $this->prepareWidgetInstance($data);
    }

    /**
     * Prepare Widget Instance data.
     *
     * @param array $data
     * @throws \Exception
     * @return array
     */
    protected function prepareWidgetInstance($data)
    {
        foreach ($data['widget_instance'] as $key => $widgetInstance) {
            $pageGroup = $widgetInstance['page_group'];

            if (!isset($widgetInstance[$pageGroup]['page_id'])) {
                $widgetInstance[$pageGroup]['page_id'] = 0;
            }
            $method = 'prepare' . str_replace(' ', '', ucwords(str_replace('_', ' ', $pageGroup))) . 'Group';
            if (!method_exists(__CLASS__, $method)) {
                throw new \Exception('Method for prepare page group "' . $method . '" is not exist.');
            }
            $widgetInstance[$pageGroup] = $this->$method($widgetInstance[$pageGroup]);
            $data['widget_instance'][$key] = $widgetInstance;
        }

        return $data;
    }

    /**
     * Prepare All Page Group.
     *
     * @param array $widgetInstancePageGroup
     * @return array
     */
    protected function prepareAllPagesGroup(array $widgetInstancePageGroup)
    {
        $widgetInstancePageGroup['layout_handle'] = 'default';
        $widgetInstancePageGroup['for'] = 'all';
        if (!isset($group['template'])) {
            $widgetInstancePageGroup['template'] = $this->widgetInstanceTemplate;
        }

        return $widgetInstancePageGroup;
    }

    /**
     * Prepare Non-Anchor Categories Page Group.
     *
     * @param array $widgetInstancePageGroup
     * @return array
     */
    protected function prepareNotanchorCategoriesGroup(array $widgetInstancePageGroup)
    {
        return $widgetInstancePageGroup['is_anchor_only'] = 0;
    }
}
