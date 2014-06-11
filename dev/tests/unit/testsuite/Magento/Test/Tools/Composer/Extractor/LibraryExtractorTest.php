<?php

namespace Magento\Test\Tools\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class LibraryExtractorTest
 *
 * @package Magento\Test\Tools\Composer\Extractor
 */
class LibraryExtractorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Library Extractor
     *
     * @var \Magento\Tools\Composer\Extractor\LibraryExtractor
     */
    protected $extractor;

    protected $parser;

    /**
     * Initial Setup
     */
    protected function setUp()
    {
        $rootDir = __DIR__ . '/../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $logger = $this->getMockBuilder('Zend_Log')
            ->disableOriginalConstructor()
            ->getMock();
        $this->parser = $objectManagerHelper->getObject('\Magento\Tools\Composer\Parser\LibraryXmlParser');
        $this->extractor = $objectManagerHelper->getObject('\Magento\Tools\Composer\Extractor\LibraryExtractor', array('rootDir' => $rootDir, 'logger' => $logger, 'parser' => $this->parser));
    }

    /**
     * Test Extract
     */
    public function testExtract()
    {
        $libraries = $this->extractor->extract();
        $this->assertEquals(sizeof($libraries), 2);
        foreach ($libraries as $library) {
            if ($library->getName() == 'Magento/Library') {
                $this->assertEquals(sizeof($library->getDependencies()), 1);
            }
        }
    }

    /**
     * Test Get Type
     */
    public function testGetType()
    {
        $this->assertEquals($this->extractor->getType(), "magento2-library");
    }

}