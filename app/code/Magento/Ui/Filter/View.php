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
use Magento\Ui\ViewFactory;
use Magento\Ui\ViewInterface;
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
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ViewFactory $viewFactory
     * @param ContentTypeFactory $contentTypeFactory
     * @param array $data
     */
    public function __construct(
        Context $renderContext,
        TemplateContext $context,
        ViewFactory $viewFactory,
        ContentTypeFactory $contentTypeFactory,
        array $data = []
    ) {
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
        $this->rootComponent->addConfigData($this, $this->viewConfiguration);

        $collection = $this->renderContext->getDataCollection($this->getParentName());
        $filterData = $this->renderContext->getFilterData(static::FILTER_VAR);
        $field = $this->getData('name');
        $value = $filterData[$field];
        $condition = $this->getCondition($value);
        if ($condition !== null) {
            $collection->addFieldToFilter($field, $condition);
        }
    }

    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        return $this->viewFactory->get($this->getData('data_type'))->prepare($value);
    }
}
