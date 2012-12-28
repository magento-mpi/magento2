<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_CacheTest extends PHPUnit_Framework_TestCase
{
    public function testGetCacheInstance()
    {
        $model = new Enterprise_PageCache_Model_Cache;
        $this->assertInstanceOf('Mage_Core_Model_Cache', $model->getCacheInstance());
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
        $this->assertFileExists($dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . '/full_page_cache');
    }
}
