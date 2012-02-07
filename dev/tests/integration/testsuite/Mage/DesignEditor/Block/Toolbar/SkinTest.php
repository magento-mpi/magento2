<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for skin block functioning
 *
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Block_Toolbar_SkinTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Skin
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_DesignEditor_Block_Toolbar_Skin();
    }

    public function testGetOptions()
    {
        Mage::getConfig()->getOptions()->setDesignDir(__DIR__ . '/../../../Core/Model/Design/Source/_files/design');
        $options = $this->_block->getOptions();

        $this->assertInternalType('array', $options);
        $this->assertNotEmpty($options);

        foreach ($options as $optGroup) {
            $this->assertInternalType('array', $optGroup);
            $this->assertArrayHasKey('label', $optGroup);
            $this->assertArrayHasKey('value', $optGroup);
            $this->assertInternalType('array', $optGroup['value']);
            foreach ($optGroup['value'] as $option) {
                $this->assertInternalType('array', $option);
                $this->assertArrayHasKey('label', $option);
                $this->assertArrayHasKey('value', $option);
                $this->assertInternalType('string', $option['label']);
                $this->assertInternalType('string', $option['value']);
            }
        }
    }

    public function  testIsSkinSelected()
    {
        $oldTheme = Mage::getDesign()->getDesignTheme();
        Mage::getDesign()->setDesignTheme('a/b/c');
        $isSelected = $this->_block->isSkinSelected('a/b/c');
        Mage::getDesign()->setDesignTheme($oldTheme);
        $this->assertTrue($isSelected);

        Mage::getDesign()->setDesignTheme('a/b/c');
        $isSelected = $this->_block->isSkinSelected('c/b/a');
        Mage::getDesign()->setDesignTheme($oldTheme);
        $this->assertFalse($isSelected);
    }

    public function testGetJsonConfigType()
    {
        $jsonConfig = $this->_block->getJsonConfig();
        $origData = json_decode($jsonConfig);
        $this->assertInstanceOf('StdClass', $origData);
    }

    /**
     * @param string $key
     * @param string $valueType
     *
     * @dataProvider getJsonConfigKeysAndValuesDataProvider
     */
    public function testGetJsonConfigKeysAndValues($key, $valueType)
    {
        $jsonConfig = $this->_block->getJsonConfig();
        $origData = (array) json_decode($jsonConfig);
        $this->assertArrayHasKey($key, $origData);
        if ($origData[$key] instanceof StdClass) {
            $origData[$key] = (array) $origData[$key];
        }
        $this->assertInternalType($valueType, $origData[$key]);
    }

    /**
     * @return array
     */
    public function getJsonConfigKeysAndValuesDataProvider()
    {
        return array(
            array('selectId', 'string'),
            array('changeSkinUrl', 'string'),
            array('backParams', 'array')
        );
    }

    public function testGetSelectHtmlId()
    {
        $value = $this->_block->getSelectHtmlId();
        $this->assertNotEmpty($value);
    }
}
