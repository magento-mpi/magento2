<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Controller\Adminhtml\Pbridge;

class Index extends \Magento\Pbridge\Controller\Adminhtml\Pbridge
{
    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('result');
    }
}
