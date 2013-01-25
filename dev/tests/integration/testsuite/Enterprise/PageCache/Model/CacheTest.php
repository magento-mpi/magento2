<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_CacheTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Varien_Io_File::rmdirRecursive(Magento_Test_Bootstrap::getInstance()->getInstallDir() . '/' . __CLASS__);
    }

    public function testGetCache()
    {
        /** @var $model Enterprise_PageCache_Model_Cache */
        $model = Mage::getModel('Enterprise_PageCache_Model_Cache');
        $this->assertInstanceOf('Mage_Core_Model_Cache', $model->getCache());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetCacheInstance()
    {
        $bootstrap = Magento_Test_Bootstrap::getInstance();
        $bootstrap->reinitialize(array(
            Mage_Core_Model_App::INIT_OPTION_DIRS => array(
                Mage_Core_Model_Dir::VAR_DIR => $bootstrap->getInstallDir() . '/' . __CLASS__
            )
        ));

        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
        $expectedDir = $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . '/full_page_cache';
        $this->assertFileNotExists($expectedDir);
        $this->assertInstanceOf('Mage_Core_Model_Cache', Enterprise_PageCache_Model_Cache::getCacheInstance());
        $this->assertFileExists($expectedDir);
    }
}
