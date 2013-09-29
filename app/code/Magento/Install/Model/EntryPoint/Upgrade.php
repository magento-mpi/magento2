<?php
/**
 * Entry point for upgrading application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Model\EntryPoint;

class Upgrade extends \Magento\Core\Model\AbstractEntryPoint
{
    /**
     * Key for passing reindexing parameter
     */
    const REINDEX = 'reindex';

    /**@#+
     * Reindexing modes
     */
    const REINDEX_INVALID = 1;
    const REINDEX_ALL = 2;
    /**@#-*/

    /**
     * Apply scheme & data updates
     */
    protected function _processRequest()
    {
        /** @var $cacheFrontendPool \Magento\Core\Model\Cache\Frontend\Pool */
        $cacheFrontendPool = $this->_objectManager->get('Magento\Core\Model\Cache\Frontend\Pool');
        /** @var $cacheFrontend \Magento\Cache\FrontendInterface */
        foreach ($cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->clean();
        }

        /** @var $updater \Magento\Core\Model\Db\Updater */
        $updater = $this->_objectManager->get('Magento\Core\Model\Db\Updater');
        $updater->updateScheme();
        $updater->updateData();

        $this->_reindex();
    }

    /**
     * Perform reindexing if requested
     */
    private function _reindex()
    {
        /** @var $config \Magento\Core\Model\Config\Primary */
        $config = $this->_objectManager->get('Magento\Core\Model\Config\Primary');
        $reindexMode = $config->getParam(self::REINDEX);
        if ($reindexMode) {
            /** @var $indexer \Magento\Index\Model\Indexer */
            $indexer = $this->_objectManager->get('Magento\Index\Model\Indexer');
            if (self::REINDEX_ALL == $reindexMode) {
                $indexer->reindexAll();
            } elseif (self::REINDEX_INVALID == $reindexMode) {
                $indexer->reindexRequired();
            }
        }
    }
}
