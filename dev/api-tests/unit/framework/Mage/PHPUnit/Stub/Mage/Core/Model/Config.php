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
 * Core configuration class stub.
 * Adds possibility to load XML configuration from both root and unit-test folders.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Stub_Mage_Core_Model_Config extends Mage_Core_Model_Config
{
    /**
     * Get etc dir with test configurations.
     *
     * @return string
     */
    public function getTestEtcDir()
    {
        return Mage_PHPUnit_Initializer_Factory::getInitializer('Mage_PHPUnit_Initializer_App')
            ->getDefaultEtcDir();
    }

    /**
     * Loads test config from test etc directory
     */
    protected function _loadTestConfig()
    {
        //load test config.
        if ($etcDir = $this->getTestEtcDir()) {
            $files = glob($etcDir.DS.'*.xml');
            foreach ($files as $file) {
                $merge = clone $this->_prototype;
                $merge->loadFile($file);
                $this->extend($merge);
            }
            if (!$this->_isLocalConfigLoaded && in_array($etcDir.DS.'local.xml', $files)) {
                $this->_isLocalConfigLoaded = true;
            }
        }
    }

    /**
     * Loads base config.
     * Merges XML files from test directory.
     *
     * @return Mage_PHPUnit_Stub_Mage_Core_Model_Config
     */
    public function loadBase()
    {
        parent::loadBase();
        $this->_loadTestConfig();
        return $this;
    }

    /**
     * Load modules configuration.
     * Merges XML files from test directory after loading all modules.
     *
     * @return Mage_PHPUnit_Stub_Mage_Core_Model_Config
     */
    public function loadModules()
    {
        parent::loadModules();
        $this->_loadTestConfig();
        return $this;
    }
}
