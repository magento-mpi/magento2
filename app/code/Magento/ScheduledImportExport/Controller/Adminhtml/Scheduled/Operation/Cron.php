<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class Cron extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Run task through http request.
     *
     * @return void
     */
    public function execute()
    {
        $result = false;
        try {
            $operationId = (int)$this->getRequest()->getParam('operation');
            $schedule = new \Magento\Framework\Object();
            $schedule->setJobCode(
                \Magento\ScheduledImportExport\Model\Scheduled\Operation::CRON_JOB_NAME_PREFIX . $operationId
            );

            /*
               We need to set default (frontend) area to send email correctly because we run cron task from backend.
               If it wouldn't be done, then in email template resources will be loaded from adminhtml area
               (in which we have only default theme) which is defined in preDispatch()

                Add: After elimination of skins and refactoring of themes we can't just switch area,
                cause we can't be sure that theme set for previous area exists in new one
            */
            $design = $this->_objectManager->get('Magento\Framework\View\DesignInterface');
            $area = $design->getArea();
            $theme = $design->getDesignTheme();
            $design->setDesignTheme(
                $design->getConfigurationDesignTheme(\Magento\Framework\App\Area::AREA_FRONTEND),
                \Magento\Framework\App\Area::AREA_FRONTEND
            );

            $result = $this->_objectManager->get('Magento\ScheduledImportExport\Model\Observer')
                ->processScheduledOperation($schedule, true);

            // restore current design area and theme
            $design->setDesignTheme($theme, $area);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        if ($result) {
            $this->messageManager->addSuccess(__('The operation ran.'));
        } else {
            $this->messageManager->addError(__('Unable to run operation'));
        }

        $this->_redirect('adminhtml/*/index');
    }
}
