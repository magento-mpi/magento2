<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

/**
 * Class Form
 */
class Form extends AbstractView implements ContextBehaviorInterface
{
    /**
     * Render layout
     *
     * @var ContextBehaviorInterface|null
     */
    protected $renderLayout;

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
            ]
        );

        $this->setConfig($config);
        $this->setRenderLayout();
        $this->renderContext->getStorage()->addComponentsData($config);

        $this->createDataProviders();
        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
    }

    /**
     * @return array|null
     */
    public function getMeta()
    {
        return $this->renderContext->getStorage()->getMeta($this->getName());
    }

    /**
     * @param array $fieldData
     * @return string
     */
    public function getFieldType(array $fieldData)
    {
        $type = '';
        if (isset($fieldData['data_type'])) {
            $type = $fieldData['data_type'];
        } else if (isset($fieldData['frontend_input'])) {
            $type = $fieldData['frontend_input'];
        }

        return $type;
    }

    /**
     * Set context component
     *
     * @param ContextBehaviorInterface $component
     * @return mixed
     */
    public function setContext(ContextBehaviorInterface $component)
    {
        // TODO: Implement setContext() method.
    }

    /**
     * Get context component
     *
     * @return ContextBehaviorInterface
     */
    public function getContext()
    {
        // TODO: Implement getContext() method.
    }

    /**
     * Render content
     *
     * @return string
     */
    public function render()
    {
        return $this->contentTypeFactory->get($this->renderContext->getAcceptType())
            ->render($this->getRenderLayout(), $this->getRenderLayout()->getContentTemplate());
    }

    /**
     * Set render layout
     *
     * @return void
     */
    protected function setRenderLayout()
    {
        if ($this->hasData('layout')) {
            $this->renderLayout = $this->getLayout()->getBlock($this->getData('layout'));
            $this->renderLayout->setContext($this);
        }
    }

    /**
     * Get render layout
     *
     * @return ContextBehaviorInterface
     */
    protected function getRenderLayout()
    {
        return isset($this->renderLayout) ? $this->renderLayout : $this;
    }
}
