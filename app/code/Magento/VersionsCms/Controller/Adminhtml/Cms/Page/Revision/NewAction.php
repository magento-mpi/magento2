<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class NewAction extends Edit implements RevisionInterface
{
    /**
     * Forward to edit
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
