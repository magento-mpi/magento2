<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Index_Adminhtml_ProcessController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize process object by request
     *
     * @return false | Mage_Index_Model_Process
     */
    protected function _initProcess()
    {
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            $process = Mage::getModel('index/process')->load($processId);
            if ($process->getId()) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Display processes grid action
     */
    public function listAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/index');
        $this->_addContent($this->getLayout()->createBlock('index/adminhtml_process'));
        $this->renderLayout();
    }

    /**
     * Reindex all data what process is responsible
     */
    public function reindexProcessAction()
    {
        $process = $this->_initProcess();
        if ($process) {
            try {
                Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');
                $process->reindexAll();
                Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->_getSession()->addSuccess(
                    Mage::helper('index')->__('Index was rebuilt successfully.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                echo $e;die();
                $this->_getSession()->addException($e,
                     Mage::helper('index')->__('Some problem with reindexing process.')
                );
            }
        } else {
            $this->_getSession()->addError(
                Mage::helper('index')->__('Can\'t initialize indexer process.')
            );
        }
        $this->loadLayout();
        $this->renderLayout();
        //$this->_redirect('*/*/list');
    }

    /**
     * Reindex pending events for index process
     */
    public function reindexEventsAction()
    {

    }

    /**
     * Rebiuld all processes index
     */
    public function reindexAllAction()
    {

    }
}