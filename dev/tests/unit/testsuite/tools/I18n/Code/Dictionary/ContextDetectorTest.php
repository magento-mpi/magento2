<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

use Magento\Tools\I18n\Code\Dictionary\ContextDetector;

class ContextDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ContextDetector
     */
    protected $_contextDetector;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        $this->_contextDetector = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\ContextDetector');
    }

    public function testModuleContextDetection()
    {
        $this->assertEquals(
            array(ContextDetector::CONTEXT_TYPE_MODULE, 'Magento_Module'),
            $this->_contextDetector->getContext('/app/code/Magento/Module/Block/Test.php')
        );
    }

    public function testThemeContextDetection()
    {
        $this->assertEquals(
            array(ContextDetector::CONTEXT_TYPE_THEME, 'theme/test.phtml'),
            $this->_contextDetector->getContext('/app/design/theme/test.phtml')
        );
    }

    public function testPubContextDetection()
    {
        $this->assertEquals(
            array(ContextDetector::CONTEXT_TYPE_PUB, 'pub/lib/module/test.phtml'),
            $this->_contextDetector->getContext('/pub/lib/module/test.phtml')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid path given: invalid_path/invalid_path
     */
    public function testInvalidPathGivenException()
    {
        $this->_contextDetector->getContext('invalid_path/invalid_path');
    }
}
