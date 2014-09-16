<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Paging;

use Magento\Ui\AbstractView;
use Magento\Ui\Configuration;

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

        $this->rootComponent = $this->getParentComponent();
        $this->configuration = new Configuration(
            $this->rootComponent->getName() . '_' . $this->getNameInLayout(),
            $this->rootComponent->getName(),
            $config
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
        $this->renderContext->getStorage()->getDataCollection($this->getParentName())
            ->setCurPage($this->renderContext->getRequestParam('page', $this->configuration->getData('current')))
            ->setPageSize($this->renderContext->getRequestParam('limit', $this->configuration->getData('pageSize')));
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
