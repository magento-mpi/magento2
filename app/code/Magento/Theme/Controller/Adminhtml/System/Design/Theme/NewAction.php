<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

class NewAction extends \Magento\Theme\Controller\Adminhtml\System\Design\Theme
{
    /**
     * Create new theme
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
