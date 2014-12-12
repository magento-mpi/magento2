<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class NewConditionHtml extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Ajax conditions
     *
     * @return void
     */
    public function execute()
    {
        $this->conditionsHtmlAction('conditions');
    }
}
