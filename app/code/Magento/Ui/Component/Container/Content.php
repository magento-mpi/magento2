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
    }

    /**
     * Set context component
     *
     * @param ContextBehaviorInterface $component
     * @return mixed
     */
    public function setContext(ContextBehaviorInterface $component)
    {
        $this->context = $component;
    }

    /**
     * Get context component
     *
     * @return ContextBehaviorInterface
     */
    public function getContext()
    {
        return isset($this->context) ? $this->context : $this;
    }

    /**
     * Is the object context
     *
     * @return bool
     */
    public function isContext()
    {
        return isset($this->context);
    }
}
