<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing;

use Magento\Ui\Context;
use Magento\Ui\AbstractView;
use \Magento\Ui\DataProvider\RowPool;
use Magento\Ui\DataProvider\OptionsFactory;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Ui\ViewFactory;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * @var RowPool
     */
    protected $rowPool;

    /**
     * @var \Magento\Ui\DataProvider\RowInterface[]
     */
    protected $dataProviderRowPoll = [];

    /**
     * Constructor
     *
     * @param OptionsFactory $optionsFactory
     * @param RowPool $rowPool
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ViewFactory $viewFactory
     * @param ContentTypeFactory $contentTypeFactory
     * @param array $data
     */
    public function __construct(
        OptionsFactory $optionsFactory,
        RowPool $rowPool,
        Context $renderContext,
        TemplateContext $context,
        ViewFactory $viewFactory,
        ContentTypeFactory $contentTypeFactory,
        array $data = []
    ) {
        $this->optionsFactory = $optionsFactory;
        $this->rowPool = $rowPool;
        parent::__construct($renderContext, $context, $viewFactory, $contentTypeFactory, $data);
    }

    /**
     * Prepare custom data
     *
     * @return void
     */
    protected function prepare()
    {
        parent::prepare();

        $this->viewConfiguration = [
            'name' => $this->getData('name'),
            'parent_name' => $this->getData('name')
        ];
        $this->globalConfig = $this->viewConfiguration; // TODO fix this

        if ($this->hasData('config')) {
            $this->viewConfiguration = array_merge_recursive($this->viewConfiguration, $this->getData('config'));
        }
        $this->addConfigData($this, $this->viewConfiguration);

        $this->renderContext->register($this->getName(), $this->getData('dataSource'));
        $this->renderContext->setMeta($this->getName(), $this->getMeta());
    }

    /**
     * Render view
     *
     * @param array $arguments
     * @return mixed|string
     */
    public function render(array $arguments = [])
    {
        $this->initialConfiguration();

        return parent::render($arguments);
    }

    /**
     * Getting collection items
     *
     * return array
     */
    public function getCollectionItems()
    {
        $items = [];
        $collection = $this->renderContext->getDataCollection($this->getName());
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
     * Get meta data
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = $this->getData('meta');
        foreach ($meta['fields'] as $key => $field) {
            if (in_array($key, ['row_url'])) {
                unset($meta['fields'][$key]);
                continue;
            }
            // TODO fixme
            if ($field['data_type'] === 'date_time') {
                $field['date_format'] = $this->_localeDate->getDateTimeFormat(
                    \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
                );
            }

            if (isset($field['options_provider'])) {
                $field['options'] = $this->optionsFactory->create($field['options_provider'])
                    ->getOptions(empty($field['options']) ? [] : $field['options']);
            }
            unset($field['options_provider']);
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
        if ($this->hasData('row_data_provider')) {
            $this->dataProviderRowPoll = $this->getData('row_data_provider');
        }
        foreach ($this->dataProviderRowPoll as $field => $data) {
            $dataRow[$field] = $this->rowPool->get($data['class'])->getData($dataRow);
        }

        return $dataRow;
    }

    /**
     * Configuration initialization
     *
     * @return void
     */
    protected function initialConfiguration()
    {
        $this->globalConfig['config']['client']['root'] = $this->getUrl($this->getData('client_root'));
        $this->globalConfig['config']['client']['ajax']['data']['component'] = $this->getNameInLayout();

        $this->globalConfig['dump']['extenders'] = [];

        $this->globalConfig['meta'] = $this->renderContext->getMeta($this->getName());
        $this->globalConfig['data']['items'] = $this->getCollectionItems();

        $countItems = $this->renderContext->registry($this->getName())->getSize();
        $this->globalConfig['data']['pages'] = ceil(
            $countItems / $this->renderContext->getRequestParam('limit', 5) // TODO fixme
        );

        $this->globalConfig['data']['totalCount'] = $countItems;
    }
}
