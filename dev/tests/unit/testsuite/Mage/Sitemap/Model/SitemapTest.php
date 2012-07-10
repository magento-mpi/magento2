<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sitemap
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sitemap_Model_SitemapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sitemap_Model_Resource_Sitemap
     */
    protected $_resourceMock;

    /**
     * Set helper mocks, create resource model mock
     */
    protected function setUp()
    {
        $helperMockCore = $this->getMock('Mage_Core_Helper_Data', array('__'));
        $helperMockCore->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        Mage::register('_helper/Mage_Core_Helper_Data', $helperMockCore, true);

        $helperMockSitemap = $this->getMock('Mage_Sitemap_Helper_Data', array(
            '__',
            'getCategoryChangefreq',
            'getProductChangefreq',
            'getPageChangefreq',
            'getCategoryPriority',
            'getProductPriority',
            'getPagePriority',
            'getMaximumLinesNumber',
            'getMaximumFileSize'
        ));
        $helperMockSitemap->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $helperMockSitemap->expects($this->any())
            ->method('getCategoryChangefreq')
            ->will($this->returnValue('daily'));
        $helperMockSitemap->expects($this->any())
            ->method('getProductChangefreq')
            ->will($this->returnValue('monthly'));
        $helperMockSitemap->expects($this->any())
            ->method('getPageChangefreq')
            ->will($this->returnValue('daily'));
        $helperMockSitemap->expects($this->any())
            ->method('getCategoryPriority')
            ->will($this->returnValue('1'));
        $helperMockSitemap->expects($this->any())
            ->method('getProductPriority')
            ->will($this->returnValue('0.5'));
        $helperMockSitemap->expects($this->any())
            ->method('getPagePriority')
            ->will($this->returnValue('0.25'));
        Mage::register('_helper/Mage_Sitemap_Helper_Data', $helperMockSitemap, true);

        $this->_resourceMock = $this->getMockBuilder('Mage_Sitemap_Model_Resource_Sitemap')
            ->setMethods(array('_construct', 'beginTransaction', 'rollBack', 'save', 'addCommitCallback', 'commit'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_resourceMock->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnSelf());
    }

    /**
     * Unset helpers from registry
     */
    protected function tearDown()
    {
        Mage::unregister('_helper/Mage_Core_Helper_Data');
        Mage::unregister('_helper/Mage_Sitemap_Helper_Data');
        unset($this->_resourceMock);
    }

    /**
     * Check not allowed sitemap path validation
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Please define correct path
     */
    public function testNotAllowedPath()
    {
        $fileMock = $this->getMockBuilder('Varien_Io_File')
            ->setMethods(array('allowedPath', 'getCleanPath'))
            ->getMock();
        $fileMock->expects($this->once())
            ->method('allowedPath')
            ->will($this->returnValue(false));

        $fileMock->expects($this->any())
            ->method('getCleanPath')
            ->will($this->returnArgument(0));

        $model = $this->_getModelMock($fileMock);
        $model->save();
    }

    /**
     * Check not exists sitemap path validation
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Please create the specified folder "%s" before saving the sitemap.
     */
    public function testPathNotExists()
    {
        $fileMock = $this->getMockBuilder('Varien_Io_File')
            ->setMethods(array('allowedPath', 'getCleanPath', 'fileExists'))
            ->getMock();
        $fileMock->expects($this->once())
            ->method('allowedPath')
            ->will($this->returnValue(true));
        $fileMock->expects($this->any())
            ->method('getCleanPath')
            ->will($this->returnArgument(0));
        $fileMock->expects($this->once())
            ->method('fileExists')
            ->will($this->returnValue(false));

        $model = $this->_getModelMock($fileMock);
        $model->save();
    }

    /**
     * Check not writable sitemap path validation
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Please make sure that "%s" is writable by web-server.
     */
    public function testPathNotWritable()
    {
        $fileMock = $this->getMockBuilder('Varien_Io_File')
            ->setMethods(array('allowedPath', 'getCleanPath', 'fileExists', 'isWriteable'))
            ->getMock();
        $fileMock->expects($this->once())
            ->method('allowedPath')
            ->will($this->returnValue(true));
        $fileMock->expects($this->any())
            ->method('getCleanPath')
            ->will($this->returnArgument(0));
        $fileMock->expects($this->once())
            ->method('fileExists')
            ->will($this->returnValue(true));
        $fileMock->expects($this->once())
            ->method('isWriteable')
            ->will($this->returnValue(false));

        /** @var $model Mage_Sitemap_Model_Sitemap */
        $model = $this->_getModelMock($fileMock);
        $model->save();
    }

    //@codingStandardsIgnoreStart
    /**
     * Check invalid chars in sitemap filename validation
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.
     */
    //@codingStandardsIgnoreEnd
    public function testFilenameInvalidChars()
    {
        $fileMock = $this->getMockBuilder('Varien_Io_File')
            ->setMethods(array('allowedPath', 'getCleanPath', 'fileExists', 'isWriteable'))
            ->getMock();
        $fileMock->expects($this->once())
            ->method('allowedPath')
            ->will($this->returnValue(true));
        $fileMock->expects($this->any())
            ->method('getCleanPath')
            ->will($this->returnArgument(0));
        $fileMock->expects($this->once())
            ->method('fileExists')
            ->will($this->returnValue(true));
        $fileMock->expects($this->once())
            ->method('isWriteable')
            ->will($this->returnValue(true));

        $model = $this->_getModelMock($fileMock);
        $model->setSitemapFilename('*sitemap?.xml');
        $model->save();
    }

    /**
     * Data provider for sitemaps
     *
     * 1) Limit set to 50000 urls and 10M per sitemap file (single file)
     * 2) Limit set to 1 url and 10M per sitemap file (multiple files, 1 record per file)
     * 3) Limit set to 50000 urls and 264 bytes per sitemap file (multiple files, 1 record per file)
     *
     * @static
     * @return array
     */
    public static function sitemapDataProvider()
    {
        $expectedSingleFile = array(
            'sitemap-1-1.xml' => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL
                . '<url>'
                . '<loc>http://store.com/category.html</loc>'
                . '<lastmod>2012-12-21T00:00:00-08:00</lastmod><changefreq>daily</changefreq><priority>1.0</priority>'
                . '</url>' . PHP_EOL
                . '<url>'
                . '<loc>http://store.com/category/sub-category.html</loc>'
                . '<lastmod>2012-12-21T00:00:00-08:00</lastmod><changefreq>daily</changefreq><priority>1.0</priority>'
                . '</url>' . PHP_EOL
                . '<url>'
                . '<loc>http://store.com/product.html</loc>'
                . '<lastmod>2012-12-21T00:00:00-08:00</lastmod><changefreq>monthly</changefreq><priority>0.5</priority>'
                . '</url>' . PHP_EOL
                . '</urlset>'
        );

        $expectedMultiFile = array(
            'sitemap-1-1.xml' => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL
                . '<url>'
                . '<loc>http://store.com/category.html</loc>'
                . '<lastmod>2012-12-21T00:00:00-08:00</lastmod><changefreq>daily</changefreq><priority>1.0</priority>'
                . '</url>' . PHP_EOL
                . '</urlset>',
            'sitemap-1-2.xml' => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL
                . '<url>'
                . '<loc>http://store.com/category/sub-category.html</loc>'
                . '<lastmod>2012-12-21T00:00:00-08:00</lastmod><changefreq>daily</changefreq><priority>1.0</priority>'
                . '</url>' . PHP_EOL
                . '</urlset>',
            'sitemap-1-3.xml' => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL
                . '<url>'
                . '<loc>http://store.com/product.html</loc>'
                . '<lastmod>2012-12-21T00:00:00-08:00</lastmod><changefreq>monthly</changefreq><priority>0.5</priority>'
                . '</url>' . PHP_EOL
                . '</urlset>',
            'sitemap.xml' => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL
                . '<sitemap>'
                . '<loc>http://store.com/sitemap-1-1.xml</loc><lastmod>2012-12-21T00:00:00-08:00</lastmod>'
                . '</sitemap>' . PHP_EOL
                . '<sitemap>'
                . '<loc>http://store.com/sitemap-1-2.xml</loc><lastmod>2012-12-21T00:00:00-08:00</lastmod>'
                . '</sitemap>' . PHP_EOL
                . '<sitemap>'
                . '<loc>http://store.com/sitemap-1-3.xml</loc><lastmod>2012-12-21T00:00:00-08:00</lastmod>'
                . '</sitemap>' . PHP_EOL
                . '</sitemapindex>'
        );

        return array(
            array(50000, 10485760, $expectedSingleFile, 5),
            array(1, 10485760, $expectedMultiFile, 14),
            array(50000, 264, $expectedMultiFile, 14)
        );
    }

    /**
     * Check generation of sitemaps
     *
     * @param int $maxLines
     * @param int $maxFileSize
     * @param array $expectedFile
     * @param int $expectedWrites
     * @dataProvider sitemapDataProvider
     */
    public function testGenerateXml($maxLines, $maxFileSize, $expectedFile, $expectedWrites)
    {
        $dateMock = $this->getMockBuilder('Mage_Core_Model_Date')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::register('_singleton/Mage_Core_Model_Date', $dateMock, true);

        $fileMock = $this->getMockBuilder('Varien_Io_File')
            ->setMethods(array('streamWrite', 'open', 'streamOpen', 'streamClose',
                'allowedPath', 'getCleanPath', 'fileExists', 'isWriteable', 'mv', 'read', 'write'))
            ->getMock();
        $this->_prepareValidFileMock($fileMock);

        // Check that all $expectedWrites lines were written
        $actualFile = array();
        $currentFile = '';
        $streamWriteCallback = function ($str) use (&$actualFile, &$currentFile) {
            if (!array_key_exists($currentFile, $actualFile)) {
                $actualFile[$currentFile] = '';
            }
            $actualFile[$currentFile] .= $str;
        };

        // Check that all expected lines were written
        $fileMock->expects($this->exactly($expectedWrites))
            ->method('streamWrite')
            ->will($this->returnCallback($streamWriteCallback));

        // Check that all expected file descriptors were created
        $fileMock->expects($this->exactly(count($expectedFile)))
            ->method('streamOpen')
            ->will($this->returnCallback(
                function ($file) use (&$currentFile) {
                    $currentFile = $file;
                }
            ));

        // Check that all file descriptors were closed
        $fileMock->expects($this->exactly(count($expectedFile)))
            ->method('streamClose');

        if (count($expectedFile) == 1) {
            $fileMock->expects($this->once())
                ->method('mv')
                ->will($this->returnCallback(
                    function ($from, $to) {
                        PHPUnit_Framework_Assert::assertEquals('sitemap-1-1.xml', $from);
                        PHPUnit_Framework_Assert::assertEquals('sitemap.xml', $to);
                    }
                ));
        }

        // Check robots txt
        $fileMock->expects($this->any())
            ->method('read')
            ->will($this->returnValue(''));
        $fileMock->expects($this->any())
            ->method('write')
            ->with($this->equalTo('/robots.txt'), $this->equalTo('Sitemap: http://store.com/sitemap.xml'));

        $helperMock = Mage::registry('_helper/Mage_Sitemap_Helper_Data');
        $helperMock->expects($this->any())
            ->method('getMaximumLinesNumber')
            ->will($this->returnValue($maxLines));
        $helperMock->expects($this->any())
            ->method('getMaximumFileSize')
            ->will($this->returnValue($maxFileSize));

        $model = $this->_getModelMock($fileMock, true);
        $model->generateXml();

        $this->assertEquals($expectedFile, $actualFile);
    }

    /**
     * Prepare file io mock with all validation passed
     *
     * @param $fileMock
     */
    protected function _prepareValidFileMock($fileMock)
    {
        $fileMock->expects($this->any())
            ->method('allowedPath')
            ->will($this->returnValue(true));
        $fileMock->expects($this->any())
            ->method('getCleanPath')
            ->will($this->returnArgument(0));
        $fileMock->expects($this->any())
            ->method('fileExists')
            ->will($this->returnValue(true));
        $fileMock->expects($this->any())
            ->method('isWriteable')
            ->will($this->returnValue(true));
    }

    /**
     * Get model mock object
     *
     * @param Varien_Io_File $fileIoMock
     * @param bool $mockBeforeSave
     * @return Mage_Sitemap_Model_Sitemap|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getModelMock($fileIoMock, $mockBeforeSave = false)
    {
        $methods = array('_construct', '_getResource', '_getBaseDir', '_getFileObject', '_afterSave',
            '_getStoreBaseUrl', '_getCurrentDateTime', '_getCategoryItemsCollection', '_getProductItemsCollection',
            '_getPageItemsCollection', '_getRobotsTxtFilePath', '_getBaseUrl');
        if ($mockBeforeSave) {
            $methods[] = '_beforeSave';
        }
        /** @var $model Mage_Sitemap_Model_Sitemap */
        $model = $this->getMockBuilder('Mage_Sitemap_Model_Sitemap')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->any())
            ->method('_getResource')
            ->will($this->returnValue($this->_resourceMock));
        $model->expects($this->any())
            ->method('_getBaseDir')
            ->will($this->returnValue('/project'));
        $model->expects($this->any())
            ->method('_getStoreBaseUrl')
            ->will($this->returnValue('http://store.com/'));
        $model->expects($this->any())
            ->method('_getFileObject')
            ->will($this->returnValue($fileIoMock));
        $model->expects($this->any())
            ->method('_getCategoryItemsCollection')
            ->will($this->returnValue(array(
                new Varien_Object(array(
                    'url' => 'category.html',
                    'updated_at' => '2012-12-21 00:00:00'
                )),
                new Varien_Object(array(
                    'url' => '/category/sub-category.html',
                    'updated_at' => '2012-12-21 00:00:00'
                ))
            )));
        $model->expects($this->any())
            ->method('_getProductItemsCollection')
            ->will($this->returnValue(array(
                new Varien_Object(array(
                    'url' => 'product.html',
                    'updated_at' => '2012-12-21 00:00:00'
                ))
            )));
        $model->expects($this->any())
            ->method('_getPageItemsCollection')
            ->will($this->returnValue(array()));
        $model->expects($this->any())
            ->method('_getCurrentDateTime')
            ->will($this->returnValue('2012-12-21T00:00:00-08:00'));
        $model->expects($this->any())
            ->method('_getRobotsTxtFilePath')
            ->will($this->returnValue('/robots.txt'));
        $model->expects($this->any())
            ->method('_getBaseUrl')
            ->will($this->returnValue('/'));

        $model->setSitemapFilename('sitemap.xml');
        $model->setStoreId(1);
        $model->setSitemapPath('/');

        return $model;
    }

    /**
     * Check addition sitemap to file robots.txt
     *
     * @dataProvider validateRowDataProvider
     */
    public function testAddSitemapToRobotsTxt($sitemapName, $replaceSitemapName, $robotsStart, $robotsFinish)
    {
        $varienFile = $this->getMockBuilder('Varien_Io_File')
            ->setMethods(array('read', 'write'))
            ->getMock();
        $model = $this->_getModelMock($varienFile);

        $varienFile->expects($this->once())->method('read')
            ->will($this->returnValue($robotsStart));
        $varienFile->expects($this->once())->method('write')
            ->with($this->equalTo('/robots.txt'), $this->equalTo($robotsFinish));

        $model->addSitemapToRobotsTxt($sitemapName, $replaceSitemapName);
    }

    /**
     * Data provider of row data and errors
     *
     * @return array
     */
    public function validateRowDataProvider()
    {
        return array(
            'empty robots file' => array(
                '$sitemapName' => 'sitemap.xml',
                '$replaceSitemapName' => null,
                '$robotsStart'  => '',
                '$robotsFinish' => 'Sitemap: http://store.com/sitemap.xml',
            ),
            'not empty robots file EOL' => array(
                '$sitemapName' => 'sitemap.xml',
                '$replaceSitemapName' => null,
                '$robotsStart'  => "User-agent: *",
                '$robotsFinish' => "User-agent: *"
                    . PHP_EOL . 'Sitemap: http://store.com/sitemap.xml',
            ),
            'not empty robots file WIN' => array(
                '$sitemapName' => 'sitemap.xml',
                '$replaceSitemapName' => null,
                '$robotsStart'  => "User-agent: *\r\n",
                '$robotsFinish' => "User-agent: *\r\n\r\nSitemap: http://store.com/sitemap.xml",
            ),
            'not empty robots file UNIX' => array(
                '$sitemapName' => 'sitemap.xml',
                '$replaceSitemapName' => null,
                '$robotsStart'  => "User-agent: *\n",
                '$robotsFinish' => "User-agent: *\n\nSitemap: http://store.com/sitemap.xml",
            ),
            'replace EOL' => array(
                '$sitemapName' => 'sitemap2.xml',
                '$replaceSitemapName' => 'http://store.com/sitemap.xml',
                '$robotsStart'  => "User-agent: *" . PHP_EOL . "Sitemap: http://store.com/sitemap.xml",
                '$robotsFinish' => "User-agent: *" . PHP_EOL . "Sitemap: http://store.com/sitemap2.xml",
            ),
            'replace WIN' => array(
                '$sitemapName' => 'sitemap2.xml',
                '$replaceSitemapName' => 'http://store.com/sitemap.xml',
                '$robotsStart'  => "User-agent: *\r\nSitemap: http://store.com/sitemap.xml",
                '$robotsFinish' => "User-agent: *\r\nSitemap: http://store.com/sitemap2.xml",
            ),
            'replace UNIX' => array(
                '$sitemapName' => 'sitemap2.xml',
                '$replaceSitemapName' => 'http://store.com/sitemap.xml',
                '$robotsStart'  => "User-agent: *\nSitemap: http://store.com/sitemap.xml",
                '$robotsFinish' => "User-agent: *\nSitemap: http://store.com/sitemap2.xml",
            ),
        );
    }
}
