<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\AbstractView;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\Context;
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
     * Filters pool
     *
     * @var FilterPool
     */
    protected $filterPool;

    /**
     * Root view component
     *
     * @var UiComponentInterface
     */
    protected $rootComponent;

    /**
     * Constructor
     *
     * @param FilterPool $filterPool
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configurationFactory
     * @param ConfigBuilderInterface $configurationBuilder
     * @param array $data
     */
    public function __construct(
        FilterPool $filterPool,
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configurationFactory,
        ConfigBuilderInterface $configurationBuilder,
        array $data = []
    ) {
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
        $this->renderContext->getStorage()->addComponentsData($this->configuration);
    }

    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        return $this->filterPool->getFilter($this->getData('data_type'))->getCondition($value);
    }
}
