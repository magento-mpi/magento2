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
     * View configuration
     *
     * @var array
     */
    protected $viewConfiguration = [
        'sizes' => [5, 10, 20, 30, 50, 100, 200],
        'pageSize' => 5,
        'current' => 1
    ];

    /**
     * Prepare custom data
     *
     * @return void
     */
    protected function prepare()
    {
        parent::prepare();
        $this->rootComponent = $this->getParentComponent();

        $config = [
            'sizes' => [5, 10, 20, 30, 50, 100, 200],
            'pageSize' => 5,
            'current' => 1
        ];
        if ($this->hasData('config')) {
            $config = array_merge($config, $this->getData('config'));
        }
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
}
