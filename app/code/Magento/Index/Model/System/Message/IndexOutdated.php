<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Index\Model\System\Message;

class IndexOutdated implements \Magento\AdminNotification\Model\System\MessageInterface
{
    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var array|null
     */
    protected $_indexes = null;

    /**
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Index\Model\Indexer $indexer,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\AuthorizationInterface $authorization
    ) {
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
            /** @var $process \Magento\Index\Model\Process */
            foreach ($processes as $process) {
                if (($process->getStatus() == \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX
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
        return $this->_authorization->isAllowed('Magento_Index::index') && $this->_getProcessesForReindex();
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
        $text = __('One or more of the Indexes are not up to date: %1', $indexList) . '. ';
        $text .= __('Please go to <a href="%1">Index Management</a> and rebuild required indexes.', $url);
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
