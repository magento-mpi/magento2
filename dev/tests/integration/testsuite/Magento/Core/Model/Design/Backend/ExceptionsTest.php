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
namespace Magento\Core\Model\Design\Backend;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Design\Backend\Exceptions
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Design\Backend\Exceptions'
        );
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
            '1' => array('search' => '/Opera/', 'value' => 'Magento/blank'),
            '2' => array('search' => '/Firefox/', 'value' => 'Magento/blank')
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
            '1' => array('search' => '/Opera/', 'value' => 'Magento/blank'),
            '2' => array('search' => '', 'value' => 'Magento/blank'),
            '3' => array('search' => '/Firefox/', 'value' => 'Magento/blank')
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
            array(array('search' => 'Opera', 'value' => 'Magento/blank'), '/Opera/i'),
            array(array('search' => '/Opera/', 'value' => 'Magento/blank'), '/Opera/'),
            array(array('search' => '#iPad|iPhone#i', 'value' => 'Magento/blank'), '#iPad|iPhone#i'),
            array(
                array('search' => 'Mozilla (3.6+)/Firefox', 'value' => 'Magento/blank'),
                '/Mozilla \\(3\\.6\\+\\)\\/Firefox/i'
            )
        );

        return $result;
    }

    /**
     * @var array $value
     * @expectedException \Magento\Framework\Model\Exception
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
            array(
                array(
                    '1' => array('search' => '/Opera/', 'value' => 'Magento/blank'),
                    '2' => array('search' => '/invalid_regexp(/', 'value' => 'Magento/blank')
                )
            ),
            array(
                array(
                    '1' => array('search' => '/invalid_regexp', 'value' => 'Magento/blank'),
                    '2' => array('search' => '/Opera/', 'value' => 'Magento/blank')
                )
            ),
            array(
                array(
                    '1' => array('search' => 'invalid_regexp/iU', 'value' => 'Magento/blank'),
                    '2' => array('search' => '/Opera/', 'value' => 'Magento/blank')
                )
            ),
            array(
                array(
                    '1' => array('search' => 'invalid_regexp#', 'value' => 'Magento/blank'),
                    '2' => array('search' => '/Opera/', 'value' => 'Magento/blank')
                )
            ),
            array(
                array(
                    '1' => array('search' => '/Firefox/'),
                    '2' => array('search' => '/Opera/', 'value' => 'Magento/blank')
                )
            ),
            array(
                array(
                    '1' => array('value' => 'Magento/blank'),
                    '2' => array('search' => '/Opera/', 'value' => 'Magento/blank')
                )
            )
        );

        return $result;
    }
}
