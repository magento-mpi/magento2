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

class Mage_DesignEditor_Model_Change_Layout_MoveTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test element names
     */
    const TEST_ELEMENT     = 'test_element';
    const CONTAINER_NAME   = 'content';
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
        'element_name'          => self::TEST_ELEMENT,
        'destination_order'     => 1,
        'origin_order'          => 1,
        'destination_container' => self::CONTAINER_NAME,
        'origin_container'      => self::CONTAINER_NAME,
        'type'                  => 'layout',
        'action_name'           => 'move',
        'element'               => self::TEST_ELEMENT,
        'after'                 => 1,
        'destination'           => self::CONTAINER_NAME,
        'custom'                => 'test_value',
    );

    public function testGetAttributes()
    {
        $data = '<move element="' . self::TEST_ELEMENT . '" after="1" custom="test_value" destination="'
            . self::CONTAINER_NAME . '"/>';
        $xml = new Varien_Simplexml_Element($data);
        $this->_model = new Mage_DesignEditor_Model_Change_Layout_Move($xml);
        $this->assertAttributeEquals($this->_attributes, '_data', $this->_model);
    }
}
