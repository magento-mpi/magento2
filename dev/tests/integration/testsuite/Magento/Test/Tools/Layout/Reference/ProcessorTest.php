<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Layout;

use Magento\Tools\Layout\Formatter;
use Magento\Tools\Layout\Reference\Processor;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var string
     */
    protected $_varDir;

    /**
     * @var string
     */
    protected $_dictionaryPath;

    /**
     * @var \Magento\Tools\Layout\Reference\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\Tools\Layout\Formatter
     */
    protected $_formatter;

    protected function setUp()
    {
        $this->_testDir = realpath(__DIR__ . DS . '_files') . DS;

        $dir = \Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Dir');
        $this->_varDir = $dir->getDir(\Magento_Core_Model_Dir::VAR_DIR) . DS . 'references' . DS;
        mkdir($this->_varDir);

        $this->_formatter = new Formatter();
        $this->_dictionaryPath = $this->_varDir . 'references.xml';

        $this->_processor = new Processor($this->_formatter, $this->_dictionaryPath);
    }

    public function tearDown()
    {
        \Magento_System_Dirs::rm($this->_varDir);
    }

    public function testGetReferences()
    {
        $this->_processor->getReferences(array($this->_testDir . 'layoutValid.xml'));
        $this->_processor->writeToFile();
        $this->assertFileEquals($this->_testDir . 'layoutValidExpectList.xml', $this->_dictionaryPath);
    }

    public function testGetReferencesWithConflictNames()
    {
        $this->_processor->getReferences(array($this->_testDir . 'layoutInvalid.xml'));
        $this->_processor->writeToFile();
        $this->assertFileEquals($this->_testDir . 'layoutInvalidExpectList.xml', $this->_dictionaryPath);
    }

    public function testUpdateReferences()
    {
        $testFile = $this->_varDir . 'layoutValid.xml';
        copy($this->_testDir . 'layoutValid.xml', $testFile);

        $layouts = array($testFile);
        $this->_processor->getReferences($layouts);
        $this->_processor->writeToFile();
        $this->assertFileEquals($this->_testDir . 'layoutValidExpectList.xml', $this->_dictionaryPath);

        $this->_processor->updateReferences($layouts);
        $this->assertFileEquals($this->_testDir . 'layoutValidExpectUpdated.xml', $testFile);
    }
}
