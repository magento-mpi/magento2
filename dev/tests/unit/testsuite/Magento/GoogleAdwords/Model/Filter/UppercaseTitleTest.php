<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAdwords\Model\Filter;

class UppercaseTitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GoogleAdwords\Model\Filter\UppercaseTitle
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\GoogleAdwords\Model\Filter\UppercaseTitle();
    }

    public function dataProviderForFilterValues()
    {
        return array(array('some name', 'Some Name'), array('test', 'Test'));
    }

    /**
     * @param string $inputValue
     * @param string $returnValue
     * @dataProvider dataProviderForFilterValues
     */
    public function testFilter($inputValue, $returnValue)
    {
        $this->assertEquals($returnValue, $this->_model->filter($inputValue));
    }
}
