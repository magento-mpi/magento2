<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * \Magento\Message\AbstractMessage test case
 */
class AbstractMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Message\AbstractMessage
     */
    protected $model;

    public function setUp()
    {
        $this->model = $this->getMockBuilder('Magento\Message\AbstractMessage')
            ->disableOriginalConstructor()
            ->setMethods(array('getType'))
            ->getMockForAbstractClass();
    }

    /**
     * @cover \Magento\Message\AbstractMessage::getText
     * @cover \Magento\Message\AbstractMessage::setText
     * @dataProvider setTextGetTextProvider
     */
    public function testSetTextGetText($text)
    {
        $this->model->setText($text);
        $this->assertEquals($text, $this->model->getText());
    }

    /**
     * @return array
     */
    public function setTextGetTextProvider()
    {
        return array(
            array(''),
            array('some text'),
        );
    }

    /**
     * @cover \Magento\Message\AbstractMessage::getIdentifier
     * @cover \Magento\Message\AbstractMessage::setIdentifier
     * @dataProvider setIdentifierGetIdentifierProvider
     */
    public function testSetIdentifierGetIdentifier($identifier)
    {
        $this->model->setIdentifier($identifier);
        $this->assertEquals($identifier, $this->model->getIdentifier());
    }

    /**
     * @return array
     */
    public function setIdentifierGetIdentifierProvider()
    {
        return array(
            array(''),
            array('some identifier'),
        );
    }

    /**
     * @cover \Magento\Message\AbstractMessage::getIsSticky
     * @cover \Magento\Message\AbstractMessage::setIsSticky
     */
    public function testSetIsStickyGetIsSticky()
    {
        $this->assertFalse($this->model->getIsSticky());
        $this->model->setIsSticky();
        $this->assertTrue($this->model->getIsSticky());
    }

    /**
     * @cover \Magento\Message\AbstractMessage::toString
     */
    public function testToString()
    {
        $someText = 'some text';
        $expectedString = MessageInterface::TYPE_SUCCESS . ': ' . $someText;

        $this->model
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->will($this->returnValue(MessageInterface::TYPE_SUCCESS));

        $this->model->setText($someText);
        $this->assertEquals($expectedString, $this->model->toString());
    }
}
