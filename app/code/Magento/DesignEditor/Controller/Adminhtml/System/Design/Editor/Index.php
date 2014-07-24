<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor;

class Index extends \Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor
{
    /**
     * Display the design editor launcher page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_resolveForwarding()) {
            $this->_renderStoreDesigner();
        }
    }
}
