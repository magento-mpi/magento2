<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing;

use Magento\Ui\Context;
use Magento\Ui\Control\ActionPool;
use Magento\Ui\ViewFactory;
use Magento\Ui\AbstractView;
use Magento\Ui\ConfigurationFactory;
use \Magento\Ui\DataProvider\RowPool;
use Magento\Ui\DataProvider\OptionsFactory;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Options provider key in data array
     */
    const OPTIONS_PROVIDER_KEY = 'options_provider';

    /**
     * Row data provider key in data array
     */
    const ROW_DATA_PROVIDER_KEY = 'row_data_provider';

    /**
     * Data provider row pool
     *
     * @var RowPool
     */
    protected $dataProviderRowPool;

    /**
     * Page action pool
     *
     * @var ActionPool
     */
    protected $actionPool;

    /**
     * Constructor
     *
     * @param ActionPool $actionPool
     * @param OptionsFactory $optionsFactory
     * @param RowPool $dataProviderRowPool
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ViewFactory $viewFactory
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigurationFactory $configurationFactory
     * @param array $data
     */
    public function __construct(
        ActionPool $actionPool,
        OptionsFactory $optionsFactory,
        RowPool $dataProviderRowPool,
        Context $renderContext,
        TemplateContext $context,
        ViewFactory $viewFactory,
        ContentTypeFactory $contentTypeFactory,
        ConfigurationFactory $configurationFactory,
        array $data = []
    ) {
        $this->actionPool = $actionPool;
        $this->optionsFactory = $optionsFactory;
        $this->dataProviderRowPool = $dataProviderRowPool;
        parent::__construct($renderContext, $context, $viewFactory, $contentTypeFactory, $configurationFactory, $data);
    }

    /**
     * Prepare component data
     *
     * @param array $arguments
     * @return $this|void
     */
    public function prepare(array $arguments = [])
    {
        parent::prepare($arguments);

        $meta = $this->getMeta();
        $config = $this->getDefaultConfiguration();

        foreach ($config['page_actions'] as $key => $action) {
            $this->actionPool->addButton(
                $key,
                $action,
                $this
            );
        }

        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }

        $this->configuration = $this->configurationFactory->create(
            [
                'name' => $this->getData('name'),
                'parentName' => $this->getData('name'),
                'configuration' => $config
            ]
        );

        $this->renderContext->getStorage()->addComponentsData($this->configuration);
        $this->renderContext->getStorage()->addMeta($this->getData('name'), $meta);
        $this->renderContext->getStorage()->addDataCollection($this->getData('name'), $this->getData('dataSource'));
    }

    /**
     * Render view
     *
     * @return mixed|string
     */
    public function render()
    {
        $this->initialConfiguration();

        return parent::render();
    }

    /**
     * Get meta data
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = $this->getData('meta');
        foreach ($meta['fields'] as $key => $field) {

            // TODO fixme
            if ($field['data_type'] === 'date') {
                $field['date_format'] = $this->_localeDate->getDateTimeFormat(
                    \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
                );
            }

            if (isset($field[static::OPTIONS_PROVIDER_KEY])) {
                $field['options'] = $this->optionsFactory->create($field[static::OPTIONS_PROVIDER_KEY])
                    ->getOptions(empty($field['options']) ? [] : $field['options']);
            }

            unset($field[static::OPTIONS_PROVIDER_KEY]);
            $meta['fields'][$key] = $field;
        }

        return $meta;
    }

    /**
     * Apply data provider to row data
     *
     * @param array $dataRow
     * @return array
     */
    protected function getDataFromDataProvider(array $dataRow)
    {
        if ($this->hasData(static::ROW_DATA_PROVIDER_KEY)) {
            foreach ($this->getData(static::ROW_DATA_PROVIDER_KEY) as $field => $data) {
                $dataRow[$field] = $this->dataProviderRowPool->get($data['class'])->getData($dataRow);
            }
        }

        return $dataRow;
    }

    /**
     * Get collection items
     *
     * return array
     */
    public function getCollectionItems()
    {
        $items = [];
        $collection = $this->renderContext->getStorage()->getDataCollection($this->getName());
        foreach ($collection->getItems() as $item) {
            $actualFields = [];
            $itemsData = $this->getDataFromDataProvider($item->getData());
            foreach (array_keys($this->getData('meta/fields')) as $field) {
                $actualFields[$field] = $itemsData[$field];
            }
            $items[] = $actualFields;
        }

        return $items;
    }

    /**
     * Configuration initialization
     *
     * @return void
     */
    protected function initialConfiguration()
    {

        $this->renderContext->getStorage()->addCloudData(
            'client',
            [
                'root' => $this->getUrl($this->getData('client_root')),
                'ajax' => [
                    'data' => [
                        'component' => $this->getNameInLayout()
                    ]
                ]
            ]
        );
        $this->renderContext->getStorage()->addCloudData('dump', ['extenders' => []]);

        $countItems = $this->renderContext->getStorage()->getDataCollection($this->getName())->getSize();
        $this->renderContext->getStorage()->addData(
            $this->getName(),
            [
                'meta_reference' => $this->getName(),
                'items' => $this->getCollectionItems(),
                'pages' => ceil($countItems / $this->renderContext->getRequestParam('limit', 20)),
                'totalCount' => $countItems
            ]
        );
    }

    /**
     * Get default parameters
     *
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return [
            'page_actions' => [
                'add' => [
                    'name' => 'add',
                    'label' => __('Add New'),
                    'class' => 'primary',
                    'url' => $this->getUrl('*/*/new')
                ]
            ]
        ];
    }
}
