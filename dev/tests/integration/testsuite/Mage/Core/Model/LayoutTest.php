<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        /* Point application to predefined layout fixtures */
        Mage::getConfig()->setOptions(array(
            'design_dir' => __DIR__ . '/_files/design',
        ));
        Mage::getDesign()->setPackageName('test')
            ->setTheme('default');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Layout();
        $this->_model->getUpdate()->addHandle('layout_test_handle_main');
        $this->_model->getUpdate()->load('layout_test_handle_extra');
    }

    public function testGetUpdate()
    {
        $this->assertInstanceOf('Mage_Core_Model_Layout_Update', $this->_model->getUpdate());
    }

    public function testGetSetArea()
    {
        $this->assertEmpty($this->_model->getArea());
        $this->_model->setArea('frontend');
        $this->assertEquals('frontend', $this->_model->getArea());
    }

    public function testGetSetDirectOutput()
    {
        $this->assertFalse($this->_model->getDirectOutput());
        $this->_model->setDirectOutput(true);
        $this->assertTrue($this->_model->getDirectOutput());
    }

    /**
     * @covers Mage_Core_Model_Layout::getAllBlocks
     * @covers Mage_Core_Model_Layout::generateBlocks
     * @covers Mage_Core_Model_Layout::getBlock
     */
    public function testGenerateXmlAndBlocks()
    {
        $this->_model->generateXml();
        /* Generate fixture
        file_put_contents(__DIR__ . '/_files/_layout_update.xml', $this->_model->getNode()->asNiceXml());
        */
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/_layout_update.xml', $this->_model->getXmlString());

        $this->assertEquals(array(), $this->_model->getAllBlocks());

        $expectedBlocks = array(
            'root',
            'head',
            'head.calendar',
            'notifications',
            'notification_baseurl',
            'cache_notifications',
            'notification_survey',
            'notification_security',
            'messages'
        );
        $this->_model->generateBlocks();

        $actualBlocks = $this->_model->getAllBlocks();
        $this->assertEquals($expectedBlocks, array_keys($actualBlocks));

        /** @var $block Mage_Adminhtml_Block_Page_Head */
        $block = $this->_model->getBlock('head');
        $this->assertEquals('Magento Admin', $block->getTitle());

        $block = $this->_model->getBlock('head.calendar');
        $this->assertSame($this->_model->getBlock('head'), $block->getParentBlock());

        /** @var $block Mage_Core_Block_Template */
        $block = $this->_model->getBlock('root');
        $this->assertEquals('popup.phtml', $block->getTemplate());
    }

    public function testSetUnsetBlock()
    {
        $expectedBlockName = 'block_' . __METHOD__;
        $expectedBlock = new Mage_Core_Block_Text();

        $this->_model->setBlock($expectedBlockName, $expectedBlock);
        $this->assertSame($expectedBlock, $this->_model->getBlock($expectedBlockName));

        $this->_model->unsetBlock($expectedBlockName);
        $this->assertFalse($this->_model->getBlock($expectedBlockName));
    }

    /**
     * @dataProvider createBlockDataProvider
     */
    public function testCreateBlock($blockType, $blockName, array $blockData, $expectedName, $expectedAnonSuffix = null)
    {
        $expectedData = $blockData + array('type' => $blockType);

        $block = $this->_model->createBlock($blockType, $blockName, $blockData);

        $this->assertEquals($this->_model, $block->getLayout());
        $this->assertRegExp($expectedName, $block->getNameInLayout());
        $this->assertEquals($expectedData, $block->getData());
        $this->assertEquals($expectedAnonSuffix, $block->getAnonSuffix());
    }

    public function createBlockDataProvider()
    {
        return array(
            'named block' => array(
                'Mage_Core_Block_Template',
                'some_block_name_full_class',
                array('type' => 'Mage_Core_Block_Template'),
                '/^some_block_name_full_class$/'
            ),
            'anonymous block' => array(
                'Mage_Core_Block_Text_List',
                '',
                array('type' => 'Mage_Core_Block_Text_List',
                'key1' => 'value1'),
                '/^ANONYMOUS_.+/'
            ),
            'anonymous suffix' => array(
                'Mage_Core_Block_Template',
                '.some_anonymous_suffix',
                array('type' => 'Mage_Core_Block_Template'),
                '/^ANONYMOUS_.+/',
                'some_anonymous_suffix'
            )
        );
    }

    /**
     * @covers Mage_Core_Model_Layout::addBlock
     * @covers Mage_Core_Model_Layout::addOutputBlock
     * @covers Mage_Core_Model_Layout::getOutput
     * @covers Mage_Core_Model_Layout::removeOutputBlock
     */
    public function testGetOutput()
    {
        $blockName = 'block_' . __METHOD__;
        $expectedText = "some_text_for_$blockName";

        $block = new Mage_Core_Block_Text();
        $block->setText($expectedText);
        $this->_model->addBlock($block, $blockName);

        $this->_model->addOutputBlock($blockName);
        $this->assertEquals($expectedText, $this->_model->getOutput());

        $this->_model->removeOutputBlock($blockName);
        $this->assertEmpty($this->_model->getOutput());
    }

    public function testGetMessagesBlock()
    {
        $this->assertInstanceOf('Mage_Core_Block_Messages', $this->_model->getMessagesBlock());
    }

    /**
     * @param string $blockType
     * @param string $expectedClassName
     * @dataProvider getBlockSingletonDataProvider
     */
    public function testGetBlockSingleton($blockType, $expectedClassName)
    {
        $block = $this->_model->getBlockSingleton($blockType);
        $this->assertInstanceOf($expectedClassName, $block);
        $this->assertSame($block, $this->_model->getBlockSingleton($blockType));
    }

    public function getBlockSingletonDataProvider()
    {
        return array(
            array('Mage_Core_Block_Text', 'Mage_Core_Block_Text')
        );
    }

    public function testHelper()
    {
        $helper = $this->_model->helper('Mage_Core_Helper_Data');
        $this->assertInstanceOf('Mage_Core_Helper_Data', $helper);
        $this->assertSame($this->_model, $helper->getLayout());
    }
}
