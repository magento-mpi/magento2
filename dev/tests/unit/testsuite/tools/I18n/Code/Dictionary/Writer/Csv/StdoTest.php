<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Writer\Csv;

use Magento\Tools\I18n\Code\Dictionary\Writer\Csv as BaseCsv;

class StdoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    protected $_handler;

    protected function setUp()
    {
        $this->_handler = STDOUT;
    }

    public function tearDown()
    {
        fclose($this->_handler);
    }

    public function testThatHandlerIsRight()
    {
        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        /** @var \Magento\Tools\I18n\Code\Dictionary\Writer\Csv $writer */
        $writer = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Writer\Csv\Stdo');

        $this->assertAttributeEquals($this->_handler, '_fileHandler', $writer);
    }
}
