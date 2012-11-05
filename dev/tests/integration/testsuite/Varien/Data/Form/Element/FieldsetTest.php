<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Data
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Varien_Data_Form_Element_Fieldset
 */
class Varien_Data_Form_Element_FieldsetTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->_fieldset = new Varien_Data_Form_Element_Fieldset(array());
    }

    /**
     * Test whether fieldset contains advanced section or not
     *
     * @dataProvider fieldsDataProvider
     */
    public function testHasAdvanced(array $fields, $expect)
    {
        foreach ($fields as $field) {
            $this->_fieldset->addField(
                $field[0],
                $field[1],
                $field[2],
                $field[3],
                $field[4]
            );
        }

        $this->assertEquals(
            $expect,
            $this->_fieldset->hasAdvanced()
        );
    }

    /**
     * Test getting advanced section label
     */
    public function testAdvancedLabel()
    {
        $this->assertNotEmpty($this->_fieldset->getAdvancedLabel());
        $label = 'Test Label';
        $this->_fieldset->setAdvancedLabel($label);
        $this->assertEquals($label, $this->_fieldset->getAdvancedLabel());
    }

    /**
     * Data provider to fill fieldset with elements
     */
    public function fieldsDataProvider()
    {
        return array(
            array(
                array(
                    array(
                        'code',
                        'text',
                        array(
                            'name'     => 'code',
                            'label'    => 'Name',
                            'class'    => 'required-entry',
                            'required' => true,
                        ),
                        false,
                        false
                    ),
                    array(
                        'tax_rate',
                        'multiselect',
                        array(
                            'name'     => 'tax_rate',
                            'label'    => 'Tax Rate',
                            'class'    => 'required-entry',
                            'values'   => array('A', 'B', 'C'),
                            'value'    => 1,
                            'required' => true,
                        ),
                        false,
                        false
                    ),
                    array(
                        'priority',
                        'text',
                        array(
                            'name'     => 'priority',
                            'label'    => 'Priority',
                            'class'    => 'validate-not-negative-number',
                            'value'    => 1,
                            'required' => true,
                            'note'     => 'Tax rates at the same priority are added, others are compounded.',
                        ),
                        false,
                        true
                    ),
                    array(
                        'priority',
                        'text',
                        array(
                            'name'     => 'priority',
                            'label'    => 'Priority',
                            'class'    => 'validate-not-negative-number',
                            'value'    => 1,
                            'required' => true,
                            'note'     => 'Tax rates at the same priority are added, others are compounded.',
                        ),
                        false,
                        true
                    )
                ),
                true
            ),
            array(
                array(
                    array(
                        'code',
                        'text',
                        array(
                            'name'     => 'code',
                            'label'    => 'Name',
                            'class'    => 'required-entry',
                            'required' => true,
                        ),
                        false,
                        false
                    ),
                    array(
                        'tax_rate',
                        'multiselect',
                        array(
                            'name'     => 'tax_rate',
                            'label'    => 'Tax Rate',
                            'class'    => 'required-entry',
                            'values'   => array('A', 'B', 'C'),
                            'value'    => 1,
                            'required' => true,
                        ),
                        false,
                        false
                    )
                ),
                false
            )
        );
    }
}