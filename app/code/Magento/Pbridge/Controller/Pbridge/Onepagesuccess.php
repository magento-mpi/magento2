<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Onepagesuccess extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Redirect to Onepage checkout success page
     *
     * @return void
     */
    public function execute()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
