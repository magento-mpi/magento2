<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Controller\Adminhtml\Index;

/**
 * Class Render
 *
 * @package Magento\Ui\Controller\Adminhtml\Index
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
