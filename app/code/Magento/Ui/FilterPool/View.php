<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FilterPool;

use Magento\Ui\Context;
use Magento\Ui\AbstractView;
use Magento\Ui\ViewFactory;
use Magento\Ui\ViewInterface;
use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Ui\Filter\FilterPool;
use Magento\Ui\Filter\View as FilterView;

/**
 * Class View
 */
class View extends AbstractView
{
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
     * @param FilterPool $filterPool
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ViewFactory $viewFactory
     * @param ContentTypeFactory $contentTypeFactory
     * @param array $data
     */
    public function __construct(
        FilterPool $filterPool,
        Context $renderContext,
        TemplateContext $context,
        ViewFactory $viewFactory,
        ContentTypeFactory $contentTypeFactory,
        array $data = []
    ) {
        $this->filterPool = $filterPool;
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

        $this->rootComponent = $this->getParentBlock()->getParentBlock();
        $this->viewConfiguration['parent_name'] = $this->rootComponent->getName();
        $this->viewConfiguration['name'] = $this->viewConfiguration['parent_name'] . '_' . $this->getNameInLayout();
        $this->viewConfiguration['types'] = $this->getListOfRequiredFilters();
        $this->rootComponent->addConfigData($this, $this->viewConfiguration);

        $this->updateDataCollection();
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $collection = $this->renderContext->getDataCollection($this->getParentName());

        $metaData = $this->renderContext->getMeta($this->getParentName());
        $metaData = $metaData['fields'];
        $filterData = $this->renderContext->getFilterData(FilterView::FILTER_VAR);
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
        $meta = $this->renderContext->getMeta($this->rootComponent->getData('name'));
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
        $metaData = $this->renderContext->getMeta($this->getParentName());
        $metaData = $metaData['fields'];
        $filters = [];
        $filterData = $this->renderContext->getFilterData(FilterView::FILTER_VAR);
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
