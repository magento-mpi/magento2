<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter;

use Magento\Ui\Context;
use Magento\Ui\AbstractView;
use Magento\Ui\Configuration;
use Magento\Ui\ViewInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Filter variable name
     */
    const FILTER_VAR = 'filter';

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
     * View configuration
     *
     * @var array
     */
    protected $viewConfiguration = [
        'types' => [
            'date' => [
                'dateFormat' => 'mm/dd/yyyy'
            ]
        ]
    ];

    /**
     * Constructor
     *
     * @param FilterPool $filterPool
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ContentTypeFactory $factory
     * @param array $data
     */
    public function __construct(
        Data $dataHelper,
        FilterPool $filterPool,
        Context $renderContext,
        TemplateContext $context,
        ContentTypeFactory $factory,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->filterPool = $filterPool;
        parent::__construct($renderContext, $context, $factory, $data);
    }

    /**
     * Prepare custom data
     *
     * @return void
     */
    protected function prepare()
    {
        parent::prepare();
        $this->rootComponent = $this->getParentComponent();

        $config = [
            'sizes' => [5, 10, 20, 30, 50, 100, 200],
            'pageSize' => 5,
            'current' => 1
        ];
        if ($this->hasData('config')) {
            $config = array_merge_recursive($config, $this->getData('config'));
        }
        $this->configuration = new Configuration(
            $this->rootComponent->getName() . '_' . $this->getNameInLayout(),
            $this->rootComponent->getName(),
            $config
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
        $filterData = $this->dataHelper->prepareFilterString($this->renderContext->getRequestParam(static::FILTER_VAR));
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
}
