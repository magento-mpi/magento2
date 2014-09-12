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
use \Magento\Ui\Provider\ProviderFactory;
use Magento\Ui\DataProvider\OptionsFactory;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * @var ProviderFactory
     */
    protected $providerFactory;

    /**
     * @var \Magento\Ui\Provider\ProviderInterface[]
     */
    protected $providerActionPoll = [];

    /**
     * Constructor
     *
     * @param OptionsFactory $optionsFactory
     * @param ProviderFactory $providerFactory
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ContentTypeFactory $contentTypeFactory
     * @param array $data
     */
    public function __construct(
        OptionsFactory $optionsFactory,
        ProviderFactory $providerFactory,
        Context $renderContext,
        TemplateContext $context,
        ContentTypeFactory $contentTypeFactory,
        array $data = []
    ) {
        $this->optionsFactory = $optionsFactory;
        $this->providerFactory = $providerFactory;
        parent::__construct($renderContext, $context, $contentTypeFactory, $data);
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
     * Create providers
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function createProviders()
    {
        $cache = [];
        if ($this->hasData('provider_action_poll')) {
            $this->providerActionPoll = $this->getData('provider_action_poll');
        }

        foreach ($this->providerActionPoll as $key => $item) {
            if (empty($cache[$item['class']])) {
                $cache[$item['class']] = $this->providerFactory->create(
                    $item['class'],
                    empty($item['arguments']) ? [] : $item['arguments']
                );
            }

            $this->providerActionPoll[$key] = $cache[$item['class']];
        }
    }

    /**
     * Render view
     *
     * @param array $arguments
     * @param string $acceptType
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html')
    {
        $this->createProviders();
        $this->initialConfiguration();

        return parent::render($arguments, $acceptType);
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
     * Get meta data
     *
     * @return array
     */
    protected function getMeta()
    {
        $meta = $this->getData('meta');
        foreach ($meta['fields'] as $key => $field) {

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
     * Getting collection items
     *
     * return array
     */
    protected function getCollectionItems()
    {
        $items = [];
        $collection = $this->renderContext->getDataCollection($this->getName());
        foreach ($collection->getItems() as $item) {
            $itemsData = [];
            foreach (array_keys($this->getData('meta/fields')) as $field) {
                $itemsData[$field] = $item->getData($field);
            }
            $items[] = $this->applyActionProviders($itemsData);
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
