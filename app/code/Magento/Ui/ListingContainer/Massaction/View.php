<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ListingContainer\Massaction;

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
        'actions' => []
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
        $config = $this->getData('config');
        if (!empty($config)) {
            $this->viewConfiguration = array_merge_recursive($this->viewConfiguration, $config);
        }
        $this->prepareActionUrl();

        $this->rootComponent->addConfigData($this, $this->viewConfiguration);
    }

    /**
     * Prepare action url
     *
     * @return void
     */
    protected function prepareActionUrl()
    {
        foreach (array_keys($this->viewConfiguration['actions']) as $actionKey) {
            $this->viewConfiguration['actions'][$actionKey]['url'] = $this->getUrl(
                $this->viewConfiguration['actions'][$actionKey]['url']
            );
        }
    }
}
