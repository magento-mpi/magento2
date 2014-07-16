<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor;

class FirstEntrance extends \Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor
{
    /**
     * Display available theme list. Only when no customized themes
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
