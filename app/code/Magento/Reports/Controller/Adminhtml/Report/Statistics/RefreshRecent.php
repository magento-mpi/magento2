<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Statistics;

use \Magento\Backend\Model\Session;

class RefreshRecent extends \Magento\Reports\Controller\Adminhtml\Report\Statistics
{
    /**
     * Refresh statistics for last 25 hours
     *
     * @return void
     */
    public function execute()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            /** @var \Magento\Framework\Stdlib\DateTime\DateInterface $currentDate */
            $currentDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface')->date();
            $date = $currentDate->subHour(25);
            foreach ($collectionsNames as $collectionName) {
                $this->_objectManager->create($collectionName)->aggregate($date);
            }
            $this->messageManager->addSuccess(__('Recent statistics have been updated.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t refresh recent statistics.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }

        if ($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('adminhtml/*');
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl('*/*'));
        }
    }
}
