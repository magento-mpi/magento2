<?php
/**
 * Entry point for upgrading application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_EntryPoint_Upgrade extends Mage_Core_Model_EntryPointAbstract
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
     * @var array
     */
    private $_params;

    /**
     * Memorize the parameters for further reusing in some methods
     *
     * @param string $baseDir
     * @param array $params
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        $baseDir, array $params = array(), Magento_ObjectManager $objectManager = null
    ) {
        $this->_params = $params;
        parent::__construct($baseDir, $params, $objectManager);
    }

    /**
     * Apply scheme & data updates
     */
    protected function _processRequest()
    {
        /** @var $cacheFrontendPool Mage_Core_Model_Cache_Frontend_Pool */
        $cacheFrontendPool = $this->_objectManager->get('Mage_Core_Model_Cache_Frontend_Pool');
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->clean();
        }

        /** @var $appState \Mage_Core_Model_App_State */
        $this->_objectManager->create('Mage_Core_Model_App_State',
            array('mode' => Mage_Core_Model_App_State::MODE_DEVELOPER), true);

        /** @var $updater \Mage_Core_Model_Db_Updater */
        $updater = $this->_objectManager->get('Mage_Core_Model_Db_Updater');
        $updater->updateScheme();
        $updater->updateData();

        $this->_reindex();
    }

    /**
     * Perform reindexing if requested
     */
    private function _reindex()
    {
        if (!empty($this->_params[self::REINDEX])) {
            $mode = $this->_params[self::REINDEX];
            /** @var $indexer Mage_Index_Model_Indexer */
            $indexer = $this->_objectManager->get('Mage_Index_Model_Indexer');
            if (self::REINDEX_ALL == $mode) {
                $indexer->reindexAll();
            } elseif (self::REINDEX_INVALID == $mode) {
                $indexer->reindexRequired();
            }
        }
    }
}
