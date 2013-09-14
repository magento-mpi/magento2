<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cms_Model_Wysiwyg_Images_StorageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected static $_baseDir;

    public static function setUpBeforeClass()
    {
        self::$_baseDir = Mage::helper('Magento\Cms\Helper\Wysiwyg\Images')->getCurrentPath() . __CLASS__;
        mkdir(self::$_baseDir, 0777);
        touch(self::$_baseDir . DIRECTORY_SEPARATOR . '1.swf');
    }

    public static function tearDownAfterClass()
    {
        \Magento\Io\File::rmdirRecursive(self::$_baseDir);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilesCollection()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setDesignTheme('magento_basic', 'adminhtml');
        /** @var $model \Magento\Cms\Model\Wysiwyg\Images\Storage */
        $model = Mage::getModel('Magento\Cms\Model\Wysiwyg\Images\Storage');
        $collection = $model->getFilesCollection(self::$_baseDir, 'media');
        $this->assertInstanceOf('Magento\Cms\Model\Wysiwyg\Images\Storage\Collection', $collection);
        foreach ($collection as $item) {
            $this->assertInstanceOf('Magento\Object', $item);
            $this->assertStringEndsWith('/1.swf', $item->getUrl());
            $this->assertStringMatchesFormat(
                'http://%s/static/adminhtml/%s/%s/Magento_Cms/images/placeholder_thumbnail.jpg',
                $item->getThumbUrl()
            );
            return;
        }
    }

    public function testGetThumbsPath()
    {
        $filesystem = new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local);
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $imageFactory = $objectManager->get('Magento\Core\Model\Image\AdapterFactory');
        $viewUrl = $objectManager->get('Magento\Core\Model\View\Url');
        $model = new \Magento\Cms\Model\Wysiwyg\Images\Storage($filesystem, $imageFactory, $viewUrl);
        $this->assertStringStartsWith(
            realpath(Magento_TestFramework_Helper_Bootstrap::getInstance()->getAppInstallDir()),
            $model->getThumbsPath()
        );
    }
}
