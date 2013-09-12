<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\I18n\Code\Dictionary;

use Magento\Tools\I18n\Code\ServiceLocator;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var string
     */
    protected $_expectedDir;

    /**
     * @var string
     */
    protected $_source;

    /**
     * @var string
     */
    protected $_outputFileName;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Generator
     */
    protected $_generator;

    /**
     * @var array
     */
    protected $_filesOptions;

    protected function setUp()
    {
        $this->_testDir = realpath(__DIR__ . '/_files');
        $this->_expectedDir  = $this->_testDir . '/expected';
        $this->_source = $this->_testDir . '/source';
        $this->_filesOptions = array(
            array(
                'type' => 'php',
                'paths' => array(
                    $this->_source . '/app/code/',
                    $this->_source . '/app/design/',
                ),
                'fileMask' => '/\.(php|phtml)$/',
            ),
            array(
                'type' => 'js',
                'paths' => array(
                    $this->_source . '/app/code/',
                    $this->_source . '/app/design/',
                    $this->_source . '/pub/lib/mage/',
                    $this->_source . '/pub/lib/varien/',
                ),
                'fileMask' => '/\.(js|phtml)$/',
            ),
            array(
                'type' => 'xml',
                'paths' => array(
                    $this->_source . '/app/code/',
                    $this->_source . '/app/design/',
                ),
                'fileMask' => '/\.xml$/',
            ),
        );
        $this->_outputFileName = $this->_testDir . '/translate.csv';

        $this->_generator = ServiceLocator::getDictionaryGenerator();
    }

    public function tearDown()
    {
        if (file_exists($this->_outputFileName)) {
            unlink($this->_outputFileName);
        }
    }

    public function testGenerationWithoutContext()
    {
        $this->_generator->generate($this->_filesOptions, $this->_outputFileName);

        $this->assertFileEquals($this->_expectedDir . '/without_context.csv', $this->_outputFileName);
    }

    public function testGenerationWithContext()
    {
        $this->_generator->generate($this->_filesOptions, $this->_outputFileName, true);

        $this->assertFileEquals($this->_expectedDir . '/with_context.csv', $this->_outputFileName);
    }
}
