<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * This test was moved to the separate file.
 * Because of fixture applying order magentoAppIsolation -> magentoDataFixture -> magentoConfigFixture
 * (https://wiki.magento.com/display/PAAS/Integration+Tests+Development+Guide
 * #IntegrationTestsDevelopmentGuide-ApplyingAnnotations)
 * config fixtures can't be applied before data fixture.
 */
class Mage_Catalog_Model_Category_CategoryImageTest extends PHPUnit_Framework_TestCase
{
    /** @var int */
    protected static $_oldLogActive;

    /** @var string */
    protected static $_oldExceptionFile;

    /** @var string */
    protected static $_oldWriterModel;

    /** @var array */
    public static $exceptions = array();

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_oldLogActive = Mage::app()->getStore()->getConfig('dev/log/active');
        self::$_oldExceptionFile = Mage::app()->getStore()->getConfig('dev/log/exception_file');
        self::$_oldWriterModel = (string) Mage::getConfig()->getNode('global/log/core/writer_model');
    }

    public static function tearDownAfterClass()
    {
        Mage::app()->getStore()->setConfig('dev/log/active', self::$_oldLogActive);
        Mage::app()->getStore()->setConfig('dev/log/exception_file', self::$_oldExceptionFile);
        Mage::getConfig()->setNode('global/log/core/writer_model', self::$_oldWriterModel);

        self::$exceptions = array();

        parent::tearDownAfterClass();
    }

    /**
     * Test that there is no exception '$_FILES array is empty' in Varien_File_Uploader::_setUploadFileId()
     * if category image was not set
     *
     * @magentoDataFixture Mage/Catalog/Model/Category/_files/category_without_image.php
     */
    public function testSaveCategoryWithoutImage()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::registry('_fixture/Mage_Catalog_Model_Category');
        $this->assertNotEmpty($category->getId());

        foreach (self::$exceptions as $exception) {
            $this->assertNotContains('$_FILES array is empty', $exception['message']);
        }
    }
}

class Stub_Mage_Catalog_Model_CategoryTest_Zend_Log_Writer_Stream extends Zend_Log_Writer_Stream
{
    public function write($event)
    {
        Mage_Catalog_Model_Category_CategoryImageTest::$exceptions[] = $event;

        parent::write($event);
    }
}
