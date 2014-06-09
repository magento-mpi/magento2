<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class FrameworkExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../../../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $silentLogger = $objectManagerHelper->getObject('\Magento\Composer\Log\Writer\QuietWriter');
        $logger = $objectManagerHelper->getObject('\Magento\Composer\Log\Log' , array('logWriter' => $silentLogger, 'debugWriter' => $silentLogger));
        $this->extractor = $objectManagerHelper->getObject('\Magento\Composer\Extractor\FrameworkExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
    }

    public function testExtract(){
        $frameworks = $this->extractor->extract();
        $this->assertEquals(sizeof($frameworks), 1);
        foreach($frameworks as $framework){
            if($framework->getName() == 'Magento/Framework'){
                $this->assertEquals(sizeof($framework->getDependencies()) , 0);
            }
        }
    }

    public function testGetType(){
        $this->assertEquals($this->extractor->getType(), "magento2-framework");
    }

    public function testCreateComponent(){
        $framework = $this->extractor->createComponent('Magento/Framework');
        $this->assertEquals(get_class($framework) , 'Magento\Composer\Model\Library');
        $this->assertEquals($framework->getName(), "Magento/Framework");

    }

}