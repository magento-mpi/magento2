<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Action;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Title
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Framework\App\Action\Title();
    }

    public function testAddPrependFalse()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend('First Title');
        $this->_view->getPage()->getConfig()->getTitle()->prepend('Second Title');
        $actual = $this->_model->get();
        $expected = array('First Title', 'Second Title');

        $this->assertEquals($expected, $actual);
    }

    public function testAddPrependTrue()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend('First Title');
        $this->_view->getPage()->getConfig()->getTitle()->append('Second Title', true);
        $actual = $this->_model->get();
        $expected = array('Second Title', 'First Title');

        $this->assertEquals($expected, $actual);
    }
}
