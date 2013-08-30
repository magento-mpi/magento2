<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Parser;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parserFirst;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parserSecond;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Parser\Composite
     */
    protected $_composite;

    protected function setUp()
    {
        $this->_parserFirst = $this->getMock('Magento\Tools\I18n\Code\Dictionary\ParserInterface');
        $this->_parserSecond = $this->getMock('Magento\Tools\I18n\Code\Dictionary\ParserInterface');

        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        $this->_composite = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Parser\Composite');
        $this->_composite->add($this->_parserFirst);
        $this->_composite->add($this->_parserSecond);
    }

    public function testParse()
    {
        $this->_parserFirst->expects($this->once())->method('parse');
        $this->_parserSecond->expects($this->once())->method('parse');

        $this->_composite->parse();
    }

    public function testGetPhrases()
    {
        $this->_parserFirst->expects($this->once())->method('getPhrases')
            ->will($this->returnValue(array('phrase1' => 'phrase1')));
        $this->_parserSecond->expects($this->once())->method('getPhrases')
            ->will($this->returnValue(array('phrase2' => 'phrase2')));

        $this->assertEquals(array('phrase1' => 'phrase1', 'phrase2' => 'phrase2'), $this->_composite->getPhrases());
    }
}
