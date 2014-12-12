<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
