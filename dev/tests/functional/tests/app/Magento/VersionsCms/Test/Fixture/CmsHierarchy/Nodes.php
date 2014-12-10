<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Fixture\CmsHierarchy;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Nodes
 * Prepare Nodes for Cms Hierarchy
 */
class Nodes implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Nodes
     *
     * @var array
     */
    protected $nodes;

    /**
     * Constructor
     *
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        } else {
            $this->data = $data;
        }
    }

    /**
     * Persist Nodes
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string|null $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset for Nodes
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        //ToDo Rewrite data for UI usability when the module will be stable.
        $presets = [
            'nodeWithOnePage' => [
                '0' => [
                    'node_id' => '0',
                    'parent_node_id' => '',
                    'page_id' => '',
                    'label' => 'Node_%isolation%',
                    'identifier' => 'Node_%isolation%',
                    'sort_order' => 0,
                    'level' => 1,
                    'top_menu_visibility' => 0,
                    'top_menu_excluded' => 0,
                    'meta_cs_enabled' => 1,
                    'meta_first_last' => 1,
                    'meta_next_previous' => 1,
                    'meta_chapter_section' => 'both',
                    'pager_frame' => 50,
                    'menu_visibility' => 1,
                    'menu_ordered' => 1,
                    'menu_brief' => 0,
                    'pager_visibility' => 1,
                ],
                '1' => [
                    'node_id' => '1',
                    'parent_node_id' => '0',
                    'page_id' => '1',
                    'label' => '404 Not Found 1',
                    'identifier' => 'no-route',
                    'sort_order' => 0,
                    'level' => 2,
                ],
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }

    /**
     * Return entities
     *
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
