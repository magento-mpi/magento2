<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class Versions extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Versions implements RevisionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }
}
