<?php

namespace Magento\Test\Tools\ComposerPackager\Magento\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class LanguagePackExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;
    protected $framework;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../../../_files/';
        $objectManagerHelper = new ObjectManager($this);
       // $logWriter = $objectManagerHelper->getObject('\Magento\Composer\Log\Writer\DefaultWriter');
        $silentLogger = $objectManagerHelper->getObject('\Magento\Composer\Log\Writer\QuietWriter');
        $logger = $objectManagerHelper->getObject('\Magento\Composer\Log\Log' , array('logWriter' => $silentLogger, 'debugWriter' => $silentLogger));
        $this->extractor = $objectManagerHelper->getObject('\Magento\Composer\Extractor\LanguagePackExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
        $this->framework = $objectManagerHelper->getObject('\Magento\Composer\Model\Framework', array('name'=>'Magento/Framework', 'version' => '2.1.0'));
    }

    public function testExtract(){
        $collection =  array('Magento/Framework'=>$this->framework);
        $lpacks = $this->extractor->extract($collection);
        $this->assertEquals(sizeof($lpacks), 3);

        foreach($lpacks as $lpack){
            if($lpack->getName() == 'Magento/fr_FR'){
                $this->assertEquals(sizeof($lpack->getDependencies()) , 2);
            }
        }
    }

    public function testGetType(){
        $this->assertEquals($this->extractor->getType(), "magento2-language");
    }

    public function testCreateComponent(){
        $module = $this->extractor->createComponent('Magento/ab_AB');
        $this->assertEquals(get_class($module) , 'Magento\Composer\Model\LanguagePack');
        $this->assertEquals($module->getName(), "Magento/ab_AB");

    }

}