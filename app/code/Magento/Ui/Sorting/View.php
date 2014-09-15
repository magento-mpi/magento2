<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Sorting;

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
        'direction' => 'asc'
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
        $this->viewConfiguration['direction'] = $this->rootComponent->getData('config/params/direction');
        $this->viewConfiguration['field'] = $this->rootComponent->getData('config/params/field');

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
        if (!empty($this->viewConfiguration['field']) && !empty($this->viewConfiguration['direction'])) {
            $this->renderContext->getDataCollection($this->getParentName())->setOrder(
                $this->renderContext->getRequestParam('sort', $this->viewConfiguration['field']),
                strtoupper($this->renderContext->getRequestParam('dir', $this->viewConfiguration['direction']))
            );
        }
    }
}
