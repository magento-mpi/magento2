<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Change_Layout_RemoveTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test element names
     */
    const TEST_ELEMENT     = 'test_element';
    /**#@-*/

    /**
     * @var Mage_DesignEditor_Model_Change_Layout_Move
     */
    protected $_model;

    /**
     * Attributes from XML data update
     *
     * @var array
     */
    protected $_attributes = array(
        'element_name' => self::TEST_ELEMENT,
        'type'         => 'layout',
        'action_name'  => 'remove',
        'name'         => self::TEST_ELEMENT,
    );

    public function testGetAttributes()
    {
        $data = '<remove name="' . self::TEST_ELEMENT . '"/>';
        $xml = new Varien_Simplexml_Element($data);
        $this->_model = new Mage_DesignEditor_Model_Change_Layout_Remove($xml);
        $this->assertAttributeEquals($this->_attributes, '_data', $this->_model);
    }
}
