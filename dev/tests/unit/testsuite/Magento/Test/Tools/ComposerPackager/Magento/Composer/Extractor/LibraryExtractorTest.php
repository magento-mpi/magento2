<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class LibraryExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../../../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $logger = $this->getMockBuilder('Zend_Log')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor = $objectManagerHelper->getObject('\Magento\Composer\Extractor\LibraryExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
    }

    public function testExtract(){
        $libraries = $this->extractor->extract();
        $this->assertEquals(sizeof($libraries), 2);
        foreach($libraries as $library){
            if($library->getName() == 'Magento/Library'){
                $this->assertEquals(sizeof($library->getDependencies()) , 1);
            }
        }
    }

    public function testGetType(){
        $this->assertEquals($this->extractor->getType(), "magento2-library");
    }

    public function testCreateComponent(){
        $library = $this->extractor->createComponent('Magento/Library');
        $this->assertEquals(get_class($library) , 'Magento\Composer\Model\Library');
        $this->assertEquals($library->getName(), "Magento/Library");

    }

}