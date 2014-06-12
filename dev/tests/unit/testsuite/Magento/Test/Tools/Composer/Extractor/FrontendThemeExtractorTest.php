<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Extractor;

use Magento\TestFramework\Helper\ObjectManager;

class FrontendThemeExtractorTest extends \PHPUnit_Framework_TestCase {
    protected $extractor;
    protected $parser;
    protected function setUp()
    {
        $rootDir = __DIR__ . '/../_files/';
        $objectManagerHelper = new ObjectManager($this);
        $logger = $this->getMockBuilder('Zend_Log')
            ->disableOriginalConstructor()
            ->getMock();
        $this->parser = $objectManagerHelper->getObject('\Magento\Tools\Composer\Parser\FrontendThemeXmlParser');
        $this->extractor = $objectManagerHelper->getObject('\Magento\Tools\Composer\Extractor\FrontendThemeExtractor', array('rootDir' => $rootDir, 'logger' => $logger, 'parser' => $this->parser));
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
        $this->assertEquals($this->extractor->getType(), "magento2-theme-frontend");
    }


}