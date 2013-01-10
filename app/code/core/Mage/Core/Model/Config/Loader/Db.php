<?php
/**
 * DB-stored application configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Db
{
    /**
     * Load config data from DB
     *
     * @return Mage_Core_Model_Config
     */
    protected function _loadDb()
    {
        Magento_Profiler::start('config');
        if ($this->getInstallDate()) {
            Magento_Profiler::start('load_db');
            $dbConf = $this->getResourceModel();
            $dbConf->loadToXml($this);
            Magento_Profiler::stop('load_db');
        }
        Magento_Profiler::stop('config');
        return $this;
    }
}