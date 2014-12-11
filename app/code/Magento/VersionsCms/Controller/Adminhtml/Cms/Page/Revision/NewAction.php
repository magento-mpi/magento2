<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class NewAction extends Edit implements RevisionInterface
{
    /**
     * Forward to edit
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
