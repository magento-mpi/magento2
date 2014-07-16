<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
