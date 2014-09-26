<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

class Tab extends AbstractView
{
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

        $this->configuration = $this->configurationFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
                'configuration' => $config
            ]
        );
        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
        $this->renderContext->getStorage()->addComponentsData($this->configuration);
    }

    /**
     * @TODO Fix it
     *
     * @return array|null
     */
    public function getMeta()
    {
        return $this->renderContext->getStorage()->getMeta($this->getName());
    }

    /**
     * @TODO Fix it
     *
     * @return array|null
     */
    public function getProviderData()
    {
        return $this->renderContext->getStorage()->getData($this->getParentName());
    }
}
