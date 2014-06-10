<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class LanguagePackExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;
    protected $framework;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $logger = $this->getMockBuilder('Zend_Log')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor = $objectManagerHelper->getObject('\Magento\Tools\Composer\Extractor\LanguagePackExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
        $this->framework = $objectManagerHelper->getObject('\Magento\Tools\Composer\Model\Package', array('name'=>'Magento/Framework', 'version' => '2.1.0'));
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


}