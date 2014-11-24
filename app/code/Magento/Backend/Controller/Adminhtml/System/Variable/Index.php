<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class Index extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * Index Action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_title->add(__('Custom Variables'));

        return $this->createPage();
    }
}
