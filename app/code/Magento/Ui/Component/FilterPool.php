<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Backend\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Ui\Component\Filter\FilterAbstract;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Ui\Component\Filter\FilterPool as FilterPoolProvider;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class FilterPool
 */
class FilterPool extends AbstractView
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
     * @var FilterPoolProvider
     */
    protected $filterPool;

    /**
     * Constructor
     *
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param Data $dataHelper
     * @param FilterPoolProvider $filterPool
     * @param DataProviderFactory $dataProviderFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        Data $dataHelper,
        FilterPoolProvider $filterPool,
        DataProviderFactory $dataProviderFactory,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->filterPool = $filterPool;
        parent::__construct(
            $context,
            $renderContext,
            $contentTypeFactory,
            $configFactory,
            $configBuilder,
            $dataProviderFactory,
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
        $configData = $this->getDefaultConfiguration();
        if ($this->hasData('config')) {
            $configData = array_merge($configData, $this->getData('config'));
        }

        $config = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
                'configuration' => $configData
            ]
        );

        $this->setConfig($config);
        $this->renderContext->getStorage()->addComponentsData($config);
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
            $this->renderContext->getRequestParam(FilterAbstract::FILTER_VAR)
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
            $this->renderContext->getRequestParam(FilterAbstract::FILTER_VAR)
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
