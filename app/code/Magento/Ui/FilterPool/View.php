<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FilterPool;

use Magento\Ui\AbstractView;
use Magento\Backend\Helper\Data;
use Magento\Ui\Filter\FilterPool;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Ui\Filter\View as FilterView;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

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
     * Constructor
     *
     * @param Data $dataHelper
     * @param FilterPool $filterPool
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configurationFactory
     * @param ConfigBuilderInterface $configurationBuilder
     * @param array $data
     */
    public function __construct(
        Data $dataHelper,
        FilterPool $filterPool,
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configurationFactory,
        ConfigBuilderInterface $configurationBuilder,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->filterPool = $filterPool;
        parent::__construct(
            $context,
            $renderContext,
            $contentTypeFactory,
            $configurationFactory,
            $configurationBuilder,
            $data
        );
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getDefaultConfiguration();
        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }

        $this->configuration = $this->configurationFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
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
        $filterData = $this->dataHelper->prepareFilterString(
            $this->renderContext->getRequestParam(FilterView::FILTER_VAR)
        );
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
     * Get list of required filters
     *
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
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        $meta = $this->renderContext->getStorage()->getMeta($this->getParentName());
        $fields = [];
        if (isset($meta['fields'])) {
            foreach ($meta['fields'] as $name => $config) {
                if (isset($config['filterable']) && $config['filterable'] === false) {
                    continue;
                }
                $fields[$name] = $config;
            }
        }
        return $fields;
    }

    /**
     * Get active filters
     *
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
            if (isset($metaData[$field]['filter_type'])) {
                $filters[$field] = [
                    'title' => $metaData[$field]['title'],
                    'current_display_value' => $value
                ];
            }
        }

        return $filters;
    }
}
