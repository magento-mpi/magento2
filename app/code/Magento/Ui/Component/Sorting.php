<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Ui\Component\AbstractView;

class Sorting extends AbstractView
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

        $this->renderContext->getStorage()->addComponentsData($this->configuration);

        $this->updateDataCollection();
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $field = $this->configuration->getData('field');
        $direction = $this->configuration->getData('direction');
        if (!empty($field) && !empty($direction)) {
            $this->renderContext->getStorage()->getDataCollection($this->getParentName())->setOrder(
                $this->renderContext->getRequestParam('sort', $field),
                strtoupper($this->renderContext->getRequestParam('dir', $direction))
            );
        }
    }

    /**
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return ['direction' => 'asc'];
    }
}
