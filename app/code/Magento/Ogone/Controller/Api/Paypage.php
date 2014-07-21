<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Controller\Api;

class Paypage extends \Magento\Ogone\Controller\Api
{
    /**
     * Display our pay page, need to Ogone payment with external pay page mode
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
