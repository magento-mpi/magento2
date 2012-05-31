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
 * Because of fixture applying order
 * (https://wiki.magento.com/display/PAAS/Integration+Tests+Development+Guide
 * #IntegrationTestsDevelopmentGuide-ApplyingAnnotations)
 * we have to move this test to the separate file
 */
class Mage_Catalog_Model_Category_CategoryImageTest extends PHPUnit_Framework_TestCase
{
    /** @var int */
    protected $_oldLogActive;

    /** @var string */
    protected $_oldExceptionFile;

    /** @var string */
    protected $_oldWriterModel;

    /** @var array */
    public static $exceptions = array();

    protected function setUp()
    {
        parent::setUp();

        $this->_oldLogActive = Mage::app()->getStore()->getConfig('dev/log/active');
        $this->_oldExceptionFile = Mage::app()->getStore()->getConfig('dev/log/exception_file');
        $this->_oldWriterModel = (string) Mage::getConfig()->getNode('global/log/core/writer_model');
    }

    protected function tearDown()
    {
        Mage::app()->getStore()->setConfig('dev/log/active', $this->_oldLogActive);
        Mage::app()->getStore()->setConfig('dev/log/exception_file', $this->_oldExceptionFile);
        Mage::getConfig()->setNode('global/log/core/writer_model', $this->_oldWriterModel);

        parent::tearDown();
    }

    /**
     * Test that there is no exception '$_FILES array is empty' in Varien_File_Uploader::_setUploadFileId()
     * if category image was no set
     */
    public function testSaveCategoryWithoutImage()
    {
        Mage::app()->getStore()->setConfig('dev/log/active', 1);
        Mage::app()->getStore()->setConfig('dev/log/exception_file', 'save_category_without_image.log');
        Mage::getConfig()->setNode('global/log/core/writer_model',
            'Stub_Mage_Catalog_Model_CategoryTest_Zend_Log_Writer_Stream'
        );

        $category = new Mage_Catalog_Model_Category();
        $category->setName('Category Without Image 1')
            ->setParentId(2)
            ->setLevel(2)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->save();

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

        return parent::write($event);
    }
}
