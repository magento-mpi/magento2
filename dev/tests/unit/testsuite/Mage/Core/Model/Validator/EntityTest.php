<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Validator_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Validator_Entity
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Validator_Entity();

        $fieldOneExactValue = new Zend_Validate_Identical('field_one_value');
        $fieldOneExactValue->setMessage("'field_one' does not match expected value");
        $fieldOneLength = new Zend_Validate_StringLength(array('min' => 10));

        $fieldTwoExactValue = new Zend_Validate_Identical('field_two_value');
        $fieldTwoExactValue->setMessage("'field_two' does not match expected value");
        $fieldTwoLength = new Zend_Validate_StringLength(array('min' => 5));

        $entityValidity = new Zend_Validate_Callback(array($this, 'isEntityValid'));
        $entityValidity->setMessage('Entity is not valid.');

        $this->assertSame($this->_model, $this->_model->addRule($fieldOneLength, 'field_one'));
        $this->assertSame($this->_model, $this->_model->addRule($fieldOneExactValue, 'field_one'));

        $this->assertSame($this->_model, $this->_model->addRule($fieldTwoLength, 'field_two'));
        $this->assertSame($this->_model, $this->_model->addRule($fieldTwoExactValue, 'field_two'));

        $this->assertSame($this->_model, $this->_model->addRule($entityValidity));
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * Entity validation routine to be used as a callback
     *
     * @param Varien_Object $entity
     * @return bool
     */
    public function isEntityValid(Varien_Object $entity)
    {
        return (bool)$entity->getData('is_valid');
    }

    public function testAddRule()
    {
        $actualResult = $this->_model->addRule(new Zend_Validate_Identical('field_one_value'), 'field_one');
        $this->assertSame($this->_model, $actualResult, 'Methods chaining is broken.');
    }

    /**
     * @param array $inputEntityData
     * @param array $expectedErrors
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $inputEntityData, array $expectedErrors)
    {
        try {
            $entity = new Varien_Object($inputEntityData);
            $this->_model->validate($entity);
            $this->fail('Validation is expected to fail.');
        } catch (Mage_Core_Exception $e) {
            $actualMessages = $e->getMessages();
            $this->assertCount(
                count($expectedErrors), $actualMessages, 'Number of messages does not meet expectations.'
            );
            foreach ($expectedErrors as $errorIndex => $expectedErrorMessage) {
                /** @var $actualMessage Mage_Core_Model_Message_Abstract */
                $actualMessage = $actualMessages[$errorIndex];
                $this->assertInstanceOf('Mage_Core_Model_Message_Error', $actualMessage);
                $this->assertEquals($expectedErrorMessage, $actualMessage->getText());
            }
        }
    }

    public function validateDataProvider()
    {
        return array(
            'only "field_one" is invalid' => array(
                array('field_one' => 'one_value', 'field_two' => 'field_two_value', 'is_valid' => true),
                array(
                    "'one_value' is less than 10 characters long",
                    "'field_one' does not match expected value",
                )
            ),
            'only "field_two" is invalid' => array(
                array('field_one' => 'field_one_value', 'field_two' => 'two_value', 'is_valid' => true),
                array("'field_two' does not match expected value")
            ),
            'entity as a whole is invalid' => array(
                array('field_one' => 'field_one_value', 'field_two' => 'field_two_value'),
                array('Entity is not valid.')
            ),
            'errors aggregation' => array(
                array('field_one' => 'one_value', 'field_two' => 'two'),
                array(
                    "'one_value' is less than 10 characters long",
                    "'field_one' does not match expected value",
                    "'two' is less than 5 characters long",
                    "'field_two' does not match expected value",
                    'Entity is not valid.',
                )
            ),
        );
    }
}
