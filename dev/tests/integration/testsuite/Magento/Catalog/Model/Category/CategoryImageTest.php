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
namespace Magento\Catalog\Model\Category;

class CategoryImageTest extends \PHPUnit_Framework_TestCase
{
    /** @var int */
    protected $_oldLogActive;

    /** @var string */
    protected $_oldExceptionFile;

    /** @var string */
    protected $_oldWriterModel;

    protected function setUp()
    {
        /** @var $configModel \Magento\Core\Model\Config */
        $configModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config');
        $this->_oldLogActive = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getConfig('dev/log/active');
        $this->_oldExceptionFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getConfig('dev/log/exception_file');
        $this->_oldWriterModel = (string)$configModel->getNode('global/log/core/writer_model');
    }

    protected function tearDown()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->setConfig('dev/log/active', $this->_oldLogActive);
        $this->_oldLogActive = null;

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->setConfig('dev/log/exception_file', $this->_oldExceptionFile);
        $this->_oldExceptionFile = null;

        /** @var $configModel \Magento\Core\Model\Config */
        $configModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config');
        $configModel->setNode('global/log/core/writer_model', $this->_oldWriterModel);
        $this->_oldWriterModel = null;

        /**
         * @TODO: refactor this test
         * Changing store configuration in such a way totally breaks the idea of application isolation.
         * Class declaration in data fixture file is dumb too.
         * Added a quick fix to be able run separate tests with "phpunit --filter testMethod"
         */
        if (class_exists('Magento\Catalog\Model\Category\CategoryImageTest\StubZendLogWriterStreamTest', false)) {
            \Magento\Catalog\Model\Category\CategoryImageTest\StubZendLogWriterStreamTest::$exceptions = array();
        }
    }

    /**
     * Test that there is no exception '$_FILES array is empty' in \Magento\File\Uploader::_setUploadFileId()
     * if category image was not set
     *
     */
    public function testSaveCategoryWithoutImage()
    {
        $this->markTestSkipped('MAGETWO-15096');

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $category \Magento\Catalog\Model\Category */
        $category = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\Catalog\Model\Category');
        $this->assertNotEmpty($category->getId());

        foreach (\Magento\Catalog\Model\Category\CategoryImageTest\StubZendLogWriterStreamTest::$exceptions
                 as $exception) {
            $this->assertNotContains('$_FILES array is empty', $exception['message']);
        }
    }
}
