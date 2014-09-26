<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

/**
 * Class MassAction
 */
class MassAction extends AbstractView
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
        array_walk_recursive(
            $config,
            function (&$item, $key, $object) {
                if ($key === 'url') {
                    $item = $object->getUrl($item);
                }
            },
            $this
        );

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
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return  ['actions' => []];
    }
}
