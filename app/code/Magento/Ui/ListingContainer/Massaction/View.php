<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ListingContainer\Massaction;

use Magento\Ui\AbstractView;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Prepare component data
     *
     * @param array $arguments
     * @return $this|void
     */
    public function prepare(array $arguments = [])
    {
        parent::prepare($arguments);

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
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return  ['actions' => []];
    }
}
