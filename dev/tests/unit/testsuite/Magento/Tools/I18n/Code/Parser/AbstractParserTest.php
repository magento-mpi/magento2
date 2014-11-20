<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Code\Parser;

class AbstractParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Parser\AbstractParser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parserMock;

    protected function setUp()
    {
        $this->_parserMock = $this->getMockForAbstractClass(
            'Magento\Tools\I18n\Code\Parser\AbstractParser',
            array(),
            '',
            false
        );
    }

    /**
     * @param array $options
     * @param string $message
     * @dataProvider dataProviderForValidateOptions
     */
    public function testValidateOptions($options, $message)
    {
        $this->setExpectedException('InvalidArgumentException', $message);

        $this->_parserMock->addAdapter('php', $this->getMock('Magento\Tools\I18n\Code\Parser\AdapterInterface'));
        $this->_parserMock->parse($options);
    }

    public function dataProviderForValidateOptions()
    {
        return array(
            array(array(array('paths' => array())), 'Missed "type" in parser options.'),
            array(array(array('type' => '', 'paths' => array())), 'Missed "type" in parser options.'),
            array(
                array(array('type' => 'wrong_type', 'paths' => array())),
                'Adapter is not set for type "wrong_type".'
            ),
            array(array(array('type' => 'php')), '"paths" in parser options must be array.'),
            array(array(array('type' => 'php', 'paths' => '')), '"paths" in parser options must be array.')
        );
    }

    public function getPhrases()
    {
        $this->assertInternalType('array', $this->_parserMock->getPhrases());
    }
}
