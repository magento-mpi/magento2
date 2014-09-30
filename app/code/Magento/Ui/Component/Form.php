<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Ui\DataProvider\DataProviderInterface;

/**
 * Class Form
 */
class Form extends AbstractView
{
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
        $this->renderContext->getStorage()->addComponentsData($config);

        $this->createDataProviders();
        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
        $this->setRenderLayout();
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
     * Render content
     *
     * @return string
     */
    public function render()
    {
        return $this->contentTypeFactory->get($this->renderContext->getAcceptType())
            ->render($this, $this->getContentTemplate());
    }

    /**
     * @return DataProviderInterface[]
     */
    public function getProviders()
    {
        $providers = [];
        if ($this->hasData('data_provider_pool')) {
            foreach ($this->getData('data_provider_pool') as $name => $config) {
                $provider = $this->renderContext->getStorage()->getDataProvider($name);
                $data = $provider->getData();
                if ($provider) {
                    $providers[] = $provider;
                }
            }
        }
        return $providers;
    }

    /**
     * Set render layout
     *
     * @return void
     */
    protected function setRenderLayout()
    {
        if ($this->hasData('layout')) {
            $layoutElement = $this->getLayout()->getBlock($this->getData('layout'));
            if ($layoutElement !== false) {
                /** @var RenderLayoutInterface $layoutElement */
                $layoutElement->prepare();
                $layoutElement->setElements($this->getElements());
                $this->setElements([$layoutElement]);
            }
        }
    }
}
