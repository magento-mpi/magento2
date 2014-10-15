<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Attribute;

class Index extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->createActionPage();
        $this->_addContent(
            $page->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Attribute')
        );
        return $page;
    }
}
