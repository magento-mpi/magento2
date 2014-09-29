<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout;

use Magento\Ui\Component\AbstractView;
use Magento\Ui\Component\ContextBehaviorInterface;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class Tabs
 */
class Tabs extends AbstractView implements ContextBehaviorInterface
{
    /**
     * Context component
     *
     * @var UiComponentInterface
     */
    protected $context;

    /**
     * Flag if changed object
     *
     * @var bool
     */
    protected $isChanged = true;

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

        $configuration = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
                'configuration' => $config
            ]
        );
        $this->createDataProviders();

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

    /**
     * Begin render container
     *
     * @return string
     */
    public function begin()
    {
        return sprintf('<div id="%s">', $this->getName());
    }

    /**
     * End render container
     *
     * @return string
     */
    public function end()
    {
        return '</div>';
    }
}
