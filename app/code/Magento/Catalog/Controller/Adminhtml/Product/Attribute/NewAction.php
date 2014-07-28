<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Attribute;

class NewAction extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
