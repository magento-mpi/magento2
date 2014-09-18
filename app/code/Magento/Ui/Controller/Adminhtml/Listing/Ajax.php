<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Controller\Adminhtml\Listing;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Ajax
 */
class Ajax extends \Magento\Backend\App\Action
{
    /**
     * @var UiComponentFactory
     */
    protected $factory;

    /**
     * @param Context $context
     * @param UiComponentFactory $factory
     */
    public function __construct(Context $context, UiComponentFactory $factory)
    {
        parent::__construct($context);
        $this->factory = $factory;
    }
    /**
     * Action for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $this->_response->appendBody(
            $this->factory->createUiComponent($this->getComponent(), $this->getName())->render()
        );
    }

    /**
     * Getting name
     *
     * @return mixed
     */
    protected function getName()
    {
        return $this->_request->getParam('name');
    }

    /**
     * Getting component
     *
     * @return mixed
     */
    protected function getComponent()
    {
        return $this->_request->getParam('component');
    }
}
