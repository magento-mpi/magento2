<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Controller\Adminhtml\Sitemap;

use \Magento\Backend\App\Action;

class Generate extends \Magento\Sitemap\Controller\Adminhtml\Sitemap
{
    /**
     * Generate sitemap
     *
     * @return void
     */
    public function execute()
    {
        // init and load sitemap model
        $id = $this->getRequest()->getParam('sitemap_id');
        $sitemap = $this->_objectManager->create('Magento\Sitemap\Model\Sitemap');
        /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
        $sitemap->load($id);
        // if sitemap record exists
        if ($sitemap->getId()) {
            try {
                $sitemap->generateXml();

                $this->messageManager->addSuccess(
                    __('The sitemap "%1" has been generated.', $sitemap->getSitemapFilename())
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong generating the sitemap.'));
            }
        } else {
            $this->messageManager->addError(__('We can\'t find a sitemap to generate.'));
        }

        // go to grid
        $this->_redirect('adminhtml/*/');
    }
}
