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
use Magento\Ui\Render;

/**
 * Class Ajax
 */
class Ajax extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Render
     */
    protected $render;

    /**
     * @param Context $context
     * @param Render $render
     */
    public function __construct(Context $context, Render $render)
    {
        parent::__construct($context);
        $this->render = $render;
    }
    /**
     * Action for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $this->_response->appendBody(
            $this->render->createUiComponent($this->getComponent(), $this->getName())->render()
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
