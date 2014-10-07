<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Render
 */
class Render extends \Magento\Ui\Controller\Adminhtml\AbstractAction
{
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
}
