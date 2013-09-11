<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_VariableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Variable
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento\Core\Model\Variable');
    }

    public function testGetSetStoreId()
    {
        $this->_model->setStoreId(1);
        $this->assertEquals(1, $this->_model->getStoreId());
    }

    public function testLoadByCode()
    {
        $this->_model->setData(array(
            'code'  => 'test_code',
            'name'  => 'test_name'
        ));
        $this->_model->save();

        $variable = Mage::getModel('Magento\Core\Model\Variable');
        $variable->loadByCode('test_code');
        $this->assertEquals($this->_model->getName(), $variable->getName());
        $this->_model->delete();
    }

    public function testGetValue()
    {
        $html = '<p>test</p>';
        $text = 'test';
        $this->_model->setData(array(
            'code'          => 'test_code',
            'html_value'    => $html,
            'plain_value'   => $text
        ));
        $this->assertEquals($html, $this->_model->getValue());
        $this->assertEquals($html, $this->_model->getValue(\Magento\Core\Model\Variable::TYPE_HTML));
        $this->assertEquals($text, $this->_model->getValue(\Magento\Core\Model\Variable::TYPE_TEXT));
    }

    public function testValidate()
    {
        $this->assertNotEmpty($this->_model->validate());
        $this->_model->setName('test')
            ->setCode('test');
        $this->assertNotEmpty($this->_model->validate());
        $this->_model->save();
        try {
            $this->assertTrue($this->_model->validate());
            $this->_model->delete();
        } catch (Exception $e) {
            $this->_model->delete();
            throw $e;
        }
    }

    public function testGetVariablesOptionArray()
    {
        $this->assertEquals(array(), $this->_model->getVariablesOptionArray());
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection();
        $collection->setStoreId(1);
        $this->assertEquals(1, $collection->getStoreId(), 'Store id setter and getter');

        $collection->addValuesToResult();
        $this->assertContains('core_variable_value', (string) $collection->getSelect());
    }
}
