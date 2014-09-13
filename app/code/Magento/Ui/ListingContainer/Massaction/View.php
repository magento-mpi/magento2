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
        'actions' => [
            [
                'value' => 'delete',
                'label' => 'Delete'
            ]
        ]
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
    }
}
