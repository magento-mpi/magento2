<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Paging;

use Magento\Ui\AbstractView;

/**
 * Class View
 */
class View extends AbstractView
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
        $defaultPage = $this->configuration->getData('current');
        $offset = $this->renderContext->getRequestParam('page', $defaultPage);
        $defaultLimit = $this->configuration->getData('pageSize');
        $size = $this->renderContext->getRequestParam('limit', $defaultLimit);
        $this->renderContext->getStorage()->getDataCollection($this->getParentName())->setLimit($offset, $size);
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
