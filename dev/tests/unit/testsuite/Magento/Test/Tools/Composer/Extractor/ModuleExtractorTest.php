<?php

namespace Magento\Test\Tools\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $logger = $this->getMockBuilder('Zend_Log')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor = $objectManagerHelper->getObject('\Magento\Tools\Composer\Extractor\ModuleExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
    }

    public function testExtract(){
        $modules = $this->extractor->extract();
        $this->assertEquals(sizeof($modules), 2);
        foreach($modules as $module){
            if($module->getName() == 'Magento/SampleModule'){
                $this->assertEquals(sizeof($module->getDependencies()) , 1);
            }
        }
    }

    public function testGetType(){
        $this->assertEquals($this->extractor->getType(), "magento2-module");
    }

}