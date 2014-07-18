<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Result extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Result Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
