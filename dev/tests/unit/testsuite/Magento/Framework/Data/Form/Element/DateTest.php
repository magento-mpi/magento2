<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Date
 */
namespace Magento\Framework\Data\Form\Element;

class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Date
     */
    protected $model;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    protected function setUp()
    {
        $this->factoryMock = $this->getMock('Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $this->collectionFactoryMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $this->escaperMock = $this->getMock('Magento\Framework\Escaper', array(), array(), '', false);
        $this->model = new Date(
            $this->factoryMock,
            $this->collectionFactoryMock,
            $this->escaperMock
        );
    }

    public function testGetElementHtmlException()
    {
        $this->setExpectedException(
            'Exception',
            'Output format is not specified. Please, specify "format" key in constructor, or set it using setFormat().'
        );
        $formMock = $this->getFormMock('never');
        $this->model->setForm($formMock);
        $this->model->getElementHtml();
    }

    /**
     * @param $fieldName
     * @dataProvider providerGetElementHtmlDateFormat
     */
    public function testGetElementHtmlDateFormat($fieldName)
    {

        $formMock = $this->getFormMock('once');
        $this->model->setForm($formMock);

        $this->model->setData(array(
                $fieldName => 'yyyy-MM-dd',
                'name' => 'test_name',
                'html_id' => 'test_name',
            ));
        $this->model->getElementHtml();
    }

    public function providerGetElementHtmlDateFormat()
    {
        return array(
            array('date_format'),
            array('format'),
        );
    }

    protected function getFormMock($exactly)
    {
        $functions = array('getFieldNameSuffix', 'getHtmlIdPrefix', 'getHtmlIdSuffix');
        $formMock = $this->getMock(
            'stdClass',
            $functions,
            array(),
            '',
            false
        );
        foreach ($functions as $method) {
            switch($exactly) {
                case 'once':
                    $count = $this->once();
                    break;
                case 'never':
                default:
                    $count = $this->never();
            }
            $formMock->expects($count)->method($method);
        }

        return $formMock;
    }
}
