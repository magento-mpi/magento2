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
class Magento_Catalog_Model_Category_CategoryImageTest extends PHPUnit_Framework_TestCase
{
    /** @var int */
    protected $_oldLogActive;

    /** @var string */
    protected $_oldExceptionFile;

    /** @var string */
    protected $_oldWriterModel;

    protected function setUp()
    {
        $this->_oldLogActive = Mage::app()->getStore()->getConfig('dev/log/active');
        $this->_oldExceptionFile = Mage::app()->getStore()->getConfig('dev/log/exception_file');
        $this->_oldWriterModel = (string) Mage::getConfig()->getNode('global/log/core/writer_model');
    }

    protected function tearDown()
    {
        Mage::app()->getStore()->setConfig('dev/log/active', $this->_oldLogActive);
        $this->_oldLogActive = null;

        Mage::app()->getStore()->setConfig('dev/log/exception_file', $this->_oldExceptionFile);
        $this->_oldExceptionFile = null;

        Mage::getConfig()->setNode('global/log/core/writer_model', $this->_oldWriterModel);
        $this->_oldWriterModel = null;

        /**
         * @TODO: refactor this test
         * Changing store configuration in such a way totally breaks the idea of application isolation.
         * Class declaration in data fixture file is dumb too.
         * Added a quick fix to be able run separate tests with "phpunit --filter testMethod"
         */
        if (class_exists('Stub_Magento_Catalog_Model_CategoryTest_Zend_Log_Writer_Stream', false)) {
            Stub_Magento_Catalog_Model_CategoryTest_Zend_Log_Writer_Stream::$exceptions = array();
        }
    }

    /**
     * Test that there is no exception '$_FILES array is empty' in Magento_File_Uploader::_setUploadFileId()
     * if category image was not set
     *
     * @magentoDataFixture Magento/Catalog/Model/Category/_files/stub_zend_log_writer_stream.php
     * @magentoDataFixture Magento/Catalog/Model/Category/_files/category_without_image.php
     */
    public function testSaveCategoryWithoutImage()
    {
        /** @var $category Magento_Catalog_Model_Category */
        $category = Mage::registry('_fixture/Magento_Catalog_Model_Category');
        $this->assertNotEmpty($category->getId());

        foreach (Stub_Magento_Catalog_Model_CategoryTest_Zend_Log_Writer_Stream::$exceptions as $exception) {
            $this->assertNotContains('$_FILES array is empty', $exception['message']);
        }
    }
}
