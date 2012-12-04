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

class Mage_DesignEditor_Model_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Layout property names
     */
    const PROPERTY_SANITIZING = '_isSanitizeBlocks';
    const PROPERTY_WRAPPING   = '_enabledWrapping';
    /**#@-*/

    /**
     * @var Mage_DesignEditor_Model_Layout
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testSanitizeLayout()
    {
        $data = file_get_contents(__DIR__ . '/_files/sanitize.xml');
        $xml = new Varien_Simplexml_Element($data);
        Mage_DesignEditor_Model_Layout::sanitizeLayout($xml);
        $this->assertStringMatchesFormatFile(__DIR__ . '/_files/sanitize_expected.txt', $xml->asNiceXml());
    }

    /**
     * Create test layout with mocked arguments
     *
     * @return Mage_DesignEditor_Model_Layout
     */
    protected function _prepareLayoutObject()
    {
        return new Mage_DesignEditor_Model_Layout(
            $this->getMock('Mage_Core_Model_BlockFactory', array(), array(), '', false),
            $this->getMock('Magento_Data_Structure', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Layout_Argument_Processor', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Layout_Translator', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Layout_ScheduledStructure', array(), array(), '', false),
            $this->getMock('Mage_DesignEditor_Block_Template', array(), array(), '', false)
        );
    }

    /**
     * Test covers both setSanitizing and setWrapping methods in one test because of similar method logic
     *
     * @param string $property
     * @param bool $value
     * @throws InvalidArgumentException
     *
     * @dataProvider flagsDataProvider
     */
    public function testFlags($property, $value)
    {
        $this->_model = $this->_prepareLayoutObject();

        switch ($property) {
            case self::PROPERTY_SANITIZING:
                $this->_model->setSanitizing($value);
                break;

            case self::PROPERTY_WRAPPING:
                $this->_model->setWrapping($value);
                break;

            default:
                throw new InvalidArgumentException('Unknown property.');
        }

        $this->assertAttributeSame($value, $property, $this->_model);
    }

    /**
     * Data provider for testFlags
     *
     * @return array
     */
    public function flagsDataProvider()
    {
        return array(
            'sanitizing_true' => array(
                '$property' => self::PROPERTY_SANITIZING,
                '$value'    => true,
            ),
            'sanitizing_false' => array(
                '$property' => self::PROPERTY_SANITIZING,
                '$value'    => false,
            ),
            'wrapping_true' => array(
                '$property' => self::PROPERTY_WRAPPING,
                '$value'    => true,
            ),
            'wrapping_false' => array(
                '$property' => self::PROPERTY_WRAPPING,
                '$value'    => false,
            ),
        );
    }
}
