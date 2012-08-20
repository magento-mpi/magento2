<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Get array of index names which require data reindex
     *
     * @return array
     */
    public function getProcessesForReindex()
    {
        $res = array();
        $processes = Mage::getSingleton('Mage_Index_Model_Indexer')->getProcessesCollection()->addEventsStats();
        /** @var $process Mage_Index_Model_Process */
        foreach ($processes as $process) {
            if (($process->getStatus() == Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX
                || $process->getEvents() > 0) && $process->getIndexer()->isVisible()
            ) {
                $res[] = $process->getIndexer()->getName();
            }
        }
        return $res;
    }

    /**
     * Get index management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/process/list');
    }

    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Index::index')) {
            return parent::_toHtml();
        }
        return '';
    }
}
