<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FilterPool;

use Magento\Backend\Helper\Data;
use Magento\Ui\Configuration;
use Magento\Ui\Context;
use Magento\Ui\AbstractView;
use Magento\Ui\ViewFactory;
use Magento\Ui\ViewInterface;
use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Ui\Filter\FilterPool;
use Magento\Ui\Filter\View as FilterView;
use Magento\Ui\ConfigurationFactory;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Data helper
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $dataHelper;

    /**
     * Filters pool
     *
     * @var FilterPool
     */
    protected $filterPool;

    /**
     * Root view component
     *
     * @var ViewInterface
     */
    protected $rootComponent;

    /**
     * Constructor
     *
     * @param Data $dataHelper
     * @param FilterPool $filterPool
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ViewFactory $viewFactory
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigurationFactory $configurationFactory
     * @param array $data
     */
    public function __construct(
        Data $dataHelper,
        FilterPool $filterPool,
        Context $renderContext,
        TemplateContext $context,
        ViewFactory $viewFactory,
        ContentTypeFactory $contentTypeFactory,
        ConfigurationFactory $configurationFactory,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->filterPool = $filterPool;
        parent::__construct($renderContext, $context, $viewFactory, $contentTypeFactory, $configurationFactory, $data);
    }

    /**
     * Prepare component data
     *
     * @param array $arguments
     * @return void
     */
    public function prepare(array $arguments = [])
    {
        parent::prepare($arguments);

        $config = [
            'types' => [
                'date' => [
                    'dateFormat' => 'mm/dd/yyyy'
                ]
            ]
        ];
        if ($this->hasData('config')) {
            $config = array_merge_recursive($config, $this->getData('config'));
        }

        $this->rootComponent = $this->getParentComponent();
        $this->configuration = $this->configurationFactory->create(
            [
                'name' => $this->rootComponent->getName() . '_' . $this->getNameInLayout(),
                'parentName' => $this->rootComponent->getName(),
                'configuration' => $config
            ]
        );

        $this->updateDataCollection();
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $collection = $this->renderContext->getStorage()->getDataCollection($this->getParentName());

        $metaData = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $metaData = $metaData['fields'];
        $filterData = $this->dataHelper->prepareFilterString($this->renderContext->getRequestParam(FilterView::FILTER_VAR));
        foreach ($filterData as $field => $value) {
            if (!isset($metaData[$field]['filter_type'])) {
                continue;
            }
            $condition = $this->filterPool->getFilter($metaData[$field]['filter_type'])->getCondition($value);
            if ($condition !== null) {
                $collection->addFieldToFilter($field, $condition);
            }
        }
    }

    /**
     * @return array
     */
    protected function getListOfRequiredFilters()
    {
        $result = [];
        foreach ($this->getFields() as $field) {
            $result[] = isset($field['filter_type']) ? $field['filter_type'] : $field['input_type'];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $this->rootComponent = $this->getParentBlock()->getParentBlock();
        $meta = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $fields = [];
        if (isset($meta['fields'])) {
            foreach ($meta['fields'] as $name => $config) {
                if (isset($config['filterable']) && $config['filterable'] == false) {
                    continue;
                }
                $fields[$name] = $config;
            }
        }
        return $fields;
    }

    /**
     * @return array
     */
    public function getActiveFilters()
    {
        $metaData = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $metaData = $metaData['fields'];
        $filters = [];
        $filterData = $this->dataHelper->prepareFilterString(
            $this->renderContext->getRequestParam(FilterView::FILTER_VAR)
        );
        foreach ($filterData as $field => $value) {
            if (!isset($metaData[$field]['filter_type'])) {
                continue;
            }
            $filters[$field] = [
                'title' => $metaData[$field]['title'],
                'current_display_value' => $value
            ];
        }
        return $filters;
    }
}
