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
use \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\MassDeleteVersions as PageMassDeleteVersions;

class MassDeleteVersions extends PageMassDeleteVersions implements RevisionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }
}
