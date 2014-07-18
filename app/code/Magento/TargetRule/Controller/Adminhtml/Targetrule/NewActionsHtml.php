<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
