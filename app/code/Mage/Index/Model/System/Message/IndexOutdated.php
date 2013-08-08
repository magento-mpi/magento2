<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Index_Model_System_Message_IndexOutdated implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @var Mage_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var array|null
     */
    protected $_indexes = null;

    /**
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Index_Model_Indexer $indexer
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Magento_Core_Model_Factory_Helper $helperFactory,
        Mage_Index_Model_Indexer $indexer,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_AuthorizationInterface $authorization
    ) {
        $this->_helperFactory = $helperFactory;
        $this->_indexer = $indexer;
        $this->_urlBuilder = $urlBuilder;
        $this->_authorization = $authorization;
    }

    /**
     * @return array
     */
    protected function _getProcessesForReindex()
    {
        if ($this->_indexes === null) {
            $this->_indexes = array();
            $processes = $this->_indexer->getProcessesCollection()->addEventsStats();
            /** @var $process Mage_Index_Model_Process */
            foreach ($processes as $process) {
                if (($process->getStatus() == Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX
                    || $process->getEvents() > 0) && $process->getIndexer()->isVisible()
                ) {
                    $this->_indexes[] = $process->getIndexer()->getName();
                }
            }
        }
        return $this->_indexes;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        $data = $this->_getProcessesForReindex() ?: array();
        return md5('OUTDATED_INDEXES' . implode(':', $data));
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_authorization->isAllowed('Mage_Index::index') && $this->_getProcessesForReindex();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $data = $this->_getProcessesForReindex() ?: array();
        $indexList = implode(', ', $data);
        $url = $this->_urlBuilder->getUrl('adminhtml/process/list');
        $helper = $this->_helperFactory->get('Mage_Index_Helper_Data');
        $text = $helper->__('One or more of the Indexes are not up to date: %s', $indexList) . '. ';
        $text .= $helper->__('Please go to <a href="%s">Index Management</a> and rebuild required indexes.', $url);
        return $text;
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
