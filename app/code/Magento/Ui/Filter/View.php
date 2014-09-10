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
     * Prepare custom data
     *
     * @return void
     */
    protected function prepare()
    {
        $this->rootComponent = $this->getLayout()->getBlock('listing');
        $this->viewConfiguration['parent_name'] = $this->rootComponent->getData('config/name');
        $this->viewConfiguration['name'] = $this->viewConfiguration['parent_name'] . '_' . $this->getNameInLayout();
        $this->rootComponent->addConfigData($this, $this->viewConfiguration);
    }
}
