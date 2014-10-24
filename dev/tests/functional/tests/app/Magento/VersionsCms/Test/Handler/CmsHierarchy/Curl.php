<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Handler\CmsHierarchy;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating CMS Hierarchy
 */
class Curl extends AbstractCurl implements CmsHierarchyInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'menu_brief' => [
            'Yes' => 1,
        ],
        'top_menu_visibility' => [
            'Yes' => 1,
        ],
    ];

    /**
     * Post request for creating Cms Hierarchy
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->replaceMappingData($fixture->getData());
        $data['pager_visibility'] = 2;
        if (isset($data['nodes_data'])) {
            $data['nodes_data'] = $this->prepareNodes($data['nodes_data']);
            $data['nodes_data'] = json_encode($data['nodes_data']);
        }
        $url = $_ENV['app_backend_url'] . 'admin/cms_hierarchy/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Cms Hierarchy creation was not successful! Response: $response");
        }
        preg_match(
            '/.*node_id":"(\d+).*' . $fixture->getIdentifier() . '/',
            $response,
            $matches
        );
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['id' => $id];
    }

    /**
     * @param array $nodes
     * @return array
     */
    protected function prepareNodes($nodes)
    {
        $nodeData = [];
        foreach ($nodes as $key => $node) {
            $newKey = '_' . $key;
            $nodeData[$newKey] = $node;
            foreach ($node as $nodeKey => $value) {
                $nodeData[$newKey][$nodeKey] = ($nodeKey == 'node_id') ? '_' . $value : $value;
                $nodeData[$newKey][$nodeKey] = ($nodeKey == 'parent_node_id' && $value !== '') ?
                    '_' . $value :
                    $nodeData[$newKey][$nodeKey];
            }
        }

        return $nodeData;
    }
}
