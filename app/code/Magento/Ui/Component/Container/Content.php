<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Container;

use Magento\Ui\Component\AbstractView;
use Magento\Ui\Component\ContextBehaviorInterface;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class Content
 */
class Content extends AbstractView implements ContextBehaviorInterface
{
    /**
     * Context component
     *
     * @var UiComponentInterface
     */
    protected $context;

    /**
     * Prepare component data
     *
     * @return $this|void
     */
    public function prepare()
    {
        $config = $this->getDefaultConfiguration();
        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }

        $configuration = $this->configurationFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
                'configuration' => $config
            ]
        );

        $this->setConfiguration($configuration);
        $this->renderContext->getStorage()->addComponentsData($configuration);
    }

    /**
     * Set context component
     *
     * @param UiComponentInterface $component
     * @return mixed
     */
    public function setContext(UiComponentInterface $component)
    {
        $this->context = $component;
    }

    /**
     * Get context component
     *
     * @return UiComponentInterface
     */
    public function getContext()
    {
        return isset($this->context) ? $this->context : $this;
    }
}
