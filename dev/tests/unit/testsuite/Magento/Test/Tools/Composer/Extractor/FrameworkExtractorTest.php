<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class FrameworkExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;

    protected function setUp()
    {
        $rootDir = __DIR__ . '/../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $logger = $this->getMockBuilder('Zend_Log')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor = $objectManagerHelper->getObject('\Magento\Tools\Composer\Extractor\FrameworkExtractor', array('rootDir' => $rootDir, 'logger' => $logger));
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

}