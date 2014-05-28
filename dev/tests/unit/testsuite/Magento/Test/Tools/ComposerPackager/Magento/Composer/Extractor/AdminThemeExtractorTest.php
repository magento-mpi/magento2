<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class AdminThemeExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../../../_files/';
        $objectManagerHelper = new ObjectManager($this);
       // $logWriter = $objectManagerHelper->getObject('\Magento\Composer\Log\Writer\DefaultWriter');
        $silentLogger = $objectManagerHelper->getObject('\Magento\Composer\Log\Writer\QuietWriter');
        $logger = $objectManagerHelper->getObject('\Magento\Composer\Log\Log' , array('logWriter' => $silentLogger, 'debugWriter' => $silentLogger));
        $this->extractor = $objectManagerHelper->getObject('\Magento\Composer\Extractor\AdminThemeExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
    }

    public function testExtract(){
        $modules = $this->extractor->extract();
        $this->assertEquals(sizeof($modules), 2);
        foreach($modules as $module){
            if($module->getName() == 'Magento/Sample-Theme'){
                $this->assertEquals(sizeof($module->getDependencies()) , 1);
            }
        }
    }

    public function testGetType(){
        $this->assertEquals($this->extractor->getType(), "magento2-theme-adminhtml");
    }

    public function testCreateComponent(){
        $module = $this->extractor->createComponent('Magento/SampleTest');
        $this->assertEquals(get_class($module) , 'Magento\Composer\Model\Theme');
        $this->assertEquals($module->getName(), "Magento/SampleTest");

    }

}