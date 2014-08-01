<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit;

use Mtf\Client\Element;

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
