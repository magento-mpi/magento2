<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Paging;

use Magento\Ui\AbstractView;
use Magento\Ui\ViewInterface;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Root view component
     *
     * @var ViewInterface
     */
    protected $rootComponent;

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

        $this->rootComponent = $this->getParentBlock()->getParentBlock();
        $this->viewConfiguration['parent_name'] = $this->rootComponent->getName();
        $this->viewConfiguration['name'] = $this->viewConfiguration['parent_name'] . '_' . $this->getNameInLayout();

        $this->rootComponent->addConfigData($this, $this->viewConfiguration);

        $this->updateDataCollection();
    }

    /**
     * Update data collection
     *
     * @return void
     */
    protected function updateDataCollection()
    {
        $this->renderContext->getDataCollection($this->getParentName())
            ->setCurPage($this->renderContext->getRequestParam('page', $this->viewConfiguration['current']))
            ->setPageSize($this->renderContext->getRequestParam('limit', $this->viewConfiguration['pageSize']));
    }
}
