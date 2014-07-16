<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category\Widget;

class Chooser extends \Magento\Catalog\Controller\Adminhtml\Category\Widget
{
    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody($this->_getCategoryTreeBlock()->toHtml());
    }
}
