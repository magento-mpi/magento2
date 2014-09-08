<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing;

use Magento\Ui\AbstractView;
use \Magento\Framework\ObjectManager;
use \Magento\Backend\Block\Template\Context;

/**
 * Class View
 */
class View extends AbstractView
{
    const DEFAULT_GRID_URL = 'mui/listing/grid';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Ui\Provider\ProviderInterface[]
     */
    protected $providerActionPoll = [];

    /**
     * @var array
     */
    protected $sortingConfig = [];

    /**
     * @param Context $context
     * @param ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(Context $context, ObjectManager $objectManager, array $data = [])
    {
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);

        $this->configuration = [
            'config' => [
                'client' => [
                    'root' => $this->getUrl(static::DEFAULT_GRID_URL)
                ]
            ]
        ];
        $this->createProviders();
        $this->initialConfiguration();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function createProviders()
    {
        $cache = [];
        if ($this->hasData('provider_action_poll')) {
            $this->providerActionPoll = $this->getData('provider_action_poll');
        }

        foreach ($this->providerActionPoll as $key => $item) {
            if (empty($cache[$item['class']])) {
                $cache[$item['class']] = $this->objectManager->create(
                    $item['class'],
                    empty($item['arguments']) ? [] : $item['arguments']
                );
                if (!($cache[$item['class']] instanceof \Magento\Ui\Provider\ProviderInterface)) {
                    throw new \Exception(
                        sprintf(
                            '%s must implement the interface \Magento\Ui\Provider\ProviderInterface',
                            $item['class']
                        )
                    );
                }
            }
            $this->providerActionPoll[$key] = $cache[$item['class']];
        }
    }

    /**
     * Get collection object
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->getData('dataSource');
    }

    /**
     * @param array $item
     * @return array
     */
    protected function applyActionProviders(array $item)
    {
        foreach ($this->providerActionPoll as $name => $provider) {
            if (!empty($item[$name])) {
                $value = $item[$name];
                $item[$name] = [];
                $item[$name] = $value;
            }
            $item[$name][] = $provider->provide($item);
        }

        return $item;
    }

    /**
     * return array
     */
    protected function getCollectionItems()
    {
        $items = [];
        /** @var \Magento\Framework\Object $row */
        $collection = $this->getCollection()->setOrder(
            $this->getRequest()->getParam('sort', $this->getData('default_sort')),
            strtoupper($this->getRequest()->getParam('dir', $this->getData('default_dir')))
        );
        foreach ($collection->getItems() as $row) {
            $rowData = [];
            foreach (array_keys($this->getData('columns')) as $column) {
                $rowData[$column] = $row->getData($column);
            }
            $items[] = $this->applyActionProviders($rowData);
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function getMetaFields()
    {
        $columns = $this->getData('columns');

        return empty($columns) ? [] : array_values($columns);
    }

    /**
     * @return void
     */
    protected function initialConfiguration()
    {
        $result['config'] = $this->hasData('config') ? $this->getData('config') : [];

        $result['meta']['fields'] = $this->getMetaFields();
        $result['data']['items'] = $this->getCollectionItems();

        $countItems = $this->getCollection()->count();
        $result['data']['pages'] = ceil($countItems / 5);
        $result['data']['totalCount'] = $countItems;

        $this->configuration = array_merge_recursive($this->configuration, $result);

        $this->sortingConfig['config']['namespace'] = $this->configuration['config']['namespace'];
        $this->sortingConfig['config']['params']['direction'] = $this->getData('default_dir');
        $this->sortingConfig['config']['params']['field'] = $this->getData('default_sort');
    }

    /**
     * Produce and return block's html output
     * This method should not be overridden. You can override _toHtml() method in descendants if needed
     *
     * @return string
     */
    public function toHtml()
    {
        // TODO FIXME PLEASE !!
        if (boolval($this->getRequest()->getParam('isAjax')) === true) {
            $this->configuration = $this->configuration['data'];
            return $this->getConfigurationJson();
        } else {
            return parent::toHtml();
        }
    }

    /**
     * Getting JSON configuration sorting data
     *
     * @return string
     */
    public function getSortingJson()
    {
        return json_encode($this->sortingConfig);
    }
}
