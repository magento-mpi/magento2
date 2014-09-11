<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter;

use Magento\Ui\AbstractView;
use Magento\Ui\Listing\View as ListingView;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * Root view component
     *
     * @var ListingView
     */
    protected $rootComponent;

    /**
     * View configuration
     *
     * @var array
     */
    protected $viewConfiguration = [
        'types' => [
            'date' => [
                'dateFormat' => 'mm/dd/yyyy'
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
