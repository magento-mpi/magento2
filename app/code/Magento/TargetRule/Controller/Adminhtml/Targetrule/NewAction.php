<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class NewAction extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Create new target rule
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
