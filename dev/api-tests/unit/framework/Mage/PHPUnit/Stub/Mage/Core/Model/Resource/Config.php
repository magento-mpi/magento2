<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub class for Mage_Core_Model_Resource_Config.
 * Needed to load real modules configs.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config extends Mage_Core_Model_Mysql4_Config
{
    /**
     * Loads real modules config from real project's etc folder
     */
    protected function _initModules()
    {
        $config = Mage::getConfig();
        $etcDir = $config->getOptions()->getEtcDir();
        $rootEtc = BP . DS . 'app' . DS . 'etc';
        $config->getOptions()->setEtcDir($rootEtc);
        $config->loadModules();
        if ($etcDir != $rootEtc) {
            $config->getOptions()->setEtcDir($etcDir);
            $config->loadModules();
        }
    }

    /**
     * Load configuration values into xml config object
     *
     * @param Mage_Core_Model_Config $xmlConfig
     * @param string $condition
     * @return Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config
     */
    public function loadToXml(Mage_Core_Model_Config $xmlConfig, $condition = null)
    {
        $this->_initModules();

        return parent::loadToXml($xmlConfig, $condition);
    }
}
