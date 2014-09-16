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
use Magento\Ui\ViewInterface;
use Magento\Framework\View\Element\Template;

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
     * @var ViewInterface
     */
    protected $rootComponent;

    /**
     * @param array $arguments
     */
    public function prepare(array $arguments = [])
    {
        parent::prepare($arguments);

        $config = $this->getDefaultConfiguration();
        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }

        $this->rootComponent = $this->getParentComponent();
        $this->configuration = $this->configurationFactory->create(
            [
                'name' => $this->rootComponent->getName() . '_' . $this->getNameInLayout(),
                'parentName' => $this->rootComponent->getName(),
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
        return $this->viewFactory->get($this->getData('data_type'))->prepare($value);
    }
}
