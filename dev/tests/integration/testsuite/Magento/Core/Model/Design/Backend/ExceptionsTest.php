<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Backend_ExceptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Design\Backend\Exceptions
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = Mage::getModel('\Magento\Core\Model\Design\Backend\Exceptions');
        $this->_model->setScope('default');
        $this->_model->setScopeId(0);
        $this->_model->setPath('design/theme/ua_regexp');
    }

    /**
     * Basic test, checks that saved value contains all required entries and is saved as an array
     * @magentoDbIsolation enabled
     */
    public function testSaveValueIsFormedNicely()
    {
        $value = array(
            '1' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            '2' => array('search' => '/Firefox/', 'value' => 'magento_blank')
        );

        $this->_model->setValue($value);
        $this->_model->save();

        $processedValue = unserialize($this->_model->getValue());
        $this->assertEquals(count($processedValue), 2, 'Number of saved values is wrong');

        $entry = $processedValue['1'];
        $this->assertArrayHasKey('search', $entry);
        $this->assertArrayHasKey('value', $entry);
        $this->assertArrayHasKey('regexp', $entry);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveEmptyValueIsSkipped()
    {
        $value = array(
            '1' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            '2' => array('search' => '', 'value' => 'magento_blank'),
            '3' => array('search' => '/Firefox/', 'value' => 'magento_blank')
        );

        $this->_model->setValue($value);
        $this->_model->save();

        $processedValue = unserialize($this->_model->getValue());
        $emptyIsSkipped = isset($processedValue['1']) && !isset($processedValue['2']) && isset($processedValue['3']);
        $this->assertTrue($emptyIsSkipped);
    }

    /**
     * @param array $designException
     * @param string $regexp
     * @dataProvider saveExceptionDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveException($designException, $regexp)
    {
        $this->_model->setValue(array('1' => $designException));
        $this->_model->save();

        $processedValue = unserialize($this->_model->getValue());
        $this->assertEquals($processedValue['1']['regexp'], $regexp);
    }

    /**
     * @return array
     */
    public function saveExceptionDataProvider()
    {
        $result = array(
            array(
                array('search' => 'Opera', 'value' => 'magento_blank'),
                '/Opera/i'
            ),
            array(
                array('search' => '/Opera/', 'value' => 'magento_blank'),
                '/Opera/'
            ),
            array(
                array('search' => '#iPad|iPhone#i', 'value' => 'magento_blank'),
                '#iPad|iPhone#i'
            ),
            array(
                array('search' => 'Mozilla (3.6+)/Firefox', 'value' => 'magento_blank'),
                '/Mozilla \\(3\\.6\\+\\)\\/Firefox/i'
            )
        );

        return $result;
    }

    /**
     * @var array $value
     * @expectedException \Magento\Core\Exception
     * @dataProvider saveWrongExceptionDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveWrongException($value)
    {
        $this->_model->setValue($value);
        $this->_model->save();
    }

    /**
     * @return array
     */
    public function saveWrongExceptionDataProvider()
    {
        $result = array(
            array(array(
                '1' => array('search' => '/Opera/', 'value' => 'magento_blank'),
                '2' => array('search' => '/invalid_regexp(/', 'value' => 'magento_blank'),
            )),
            array(array(
                '1' => array('search' => '/invalid_regexp', 'value' => 'magento_blank'),
                '2' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            )),
            array(array(
                '1' => array('search' => 'invalid_regexp/iU', 'value' => 'magento_blank'),
                '2' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            )),
            array(array(
                '1' => array('search' => 'invalid_regexp#', 'value' => 'magento_blank'),
                '2' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            )),
            array(array(
                '1' => array('search' => '/Firefox/'),
                '2' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            )),
            array(array(
                '1' => array('value' => 'magento_blank'),
                '2' => array('search' => '/Opera/', 'value' => 'magento_blank'),
            ))
        );

        return $result;
    }
}
