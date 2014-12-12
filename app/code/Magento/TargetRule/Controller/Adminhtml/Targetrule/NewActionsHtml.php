<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class NewActionsHtml extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->conditionsHtmlAction('actions');
    }
}
