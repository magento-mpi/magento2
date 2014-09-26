<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

/**
 * Class Paging
 */
class Paging extends AbstractView
{
    /**
     * Prepare component data
     *
     * @return void
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

        $this->setConfiguration($configuration);
        $this->renderContext->getStorage()->addComponentsData($configuration);
        $this->updateDataCollection();
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $this->renderContext->getStorage()->getDataCollection($this->getParentName())
            ->setCurPage($this->renderContext->getRequestParam('page', $this->config->getData('current')))
            ->setPageSize($this->renderContext->getRequestParam('limit', $this->config->getData('pageSize')));
    }

    /**
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return  [
            'sizes' => [20, 30, 50, 100, 200],
            'pageSize' => 20,
            'current' => 1
        ];
    }
}
