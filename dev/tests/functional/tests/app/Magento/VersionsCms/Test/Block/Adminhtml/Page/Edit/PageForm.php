<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit;


/**
 * Class PageForm
 * Backend Cms Page edit page
 */
class PageForm extends \Magento\Cms\Test\Block\Adminhtml\Page\Edit\PageForm
{
    /**
     * Get 'Currently Published Revision' link text
     *
     * @return string
     */
    public function getCurrentlyPublishedRevisionText()
    {
        return $this->_rootElement->find($this->currentlyPublishedRevision)->getText();
    }
}
