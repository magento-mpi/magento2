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
    protected $_layout;

    public static function setUpBeforeClass()
    {
        /* Point application to predefined layout fixtures */
        Mage::getConfig()->setOptions(array(
            'design_dir' => __DIR__ . '/_files/design',
        ));
        Mage::getDesign()->setDesignTheme('test/default/default');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $this->_layout = new Mage_Core_Model_Layout();
        $this->_layout->getUpdate()->addHandle('layout_test_handle_main');
        $this->_layout->getUpdate()->load('layout_test_handle_extra');
    }

    public function testGetUpdate()
    {
        $this->assertInstanceOf('Mage_Core_Model_Layout_Update', $this->_layout->getUpdate());
    }

    public function testGetSetArea()
    {
        $this->assertEmpty($this->_layout->getArea());
        $this->_layout->setArea('frontend');
        $this->assertEquals('frontend', $this->_layout->getArea());
    }

    public function testGetSetDirectOutput()
    {
        $this->assertFalse($this->_layout->isDirectOutput());
        $this->_layout->setDirectOutput(true);
        $this->assertTrue($this->_layout->isDirectOutput());
    }

    /**
     * @covers Mage_Core_Model_Layout::getAllBlocks
     * @covers Mage_Core_Model_Layout::generateBlocks
     * @covers Mage_Core_Model_Layout::getBlock
     */
    public function testGenerateXmlAndBlocks()
    {
        $this->_layout->generateXml();
        /* Generate fixture
        file_put_contents(__DIR__ . '/_files/_layout_update.xml', $this->_model->getNode()->asNiceXml());
        */
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/_layout_update.xml', $this->_layout->getXmlString());

        $this->assertEquals(array(), $this->_layout->getAllBlocks());

        $expectedBlocks = array(
            'root',
            'head',
            'head.calendar',
            'notifications',
            'notification_baseurl',
            'cache_notifications',
            'notification_survey',
            'notification_security',
            'messages',
            'index_notifications',
            'index_notifications_copy'
        );
        $this->_layout->generateBlocks();

        $actualBlocks = $this->_layout->getAllBlocks();
        $this->assertEquals($expectedBlocks, array_keys($actualBlocks));

        /** @var $block Mage_Adminhtml_Block_Page_Head */
        $block = $this->_layout->getBlock('head');
        $this->assertEquals('Magento Admin', $block->getTitle());

        $block = $this->_layout->getBlock('head.calendar');
        $this->assertSame($this->_layout->getBlock('head'), $block->getParentBlock());

        /** @var $block Mage_Core_Block_Template */
        $block = $this->_layout->getBlock('root');
        $this->assertEquals('popup.phtml', $block->getTemplate());
    }

    public function testSetUnsetBlock()
    {
        $expectedBlockName = 'block_' . __METHOD__;
        $expectedBlock = new Mage_Core_Block_Text();

        $this->_layout->setBlock($expectedBlockName, $expectedBlock);
        $this->assertSame($expectedBlock, $this->_layout->getBlock($expectedBlockName));

        $this->_layout->unsetElement($expectedBlockName);
        $this->assertFalse($this->_layout->getBlock($expectedBlockName));
        $this->assertFalse($this->_layout->hasElement($expectedBlockName));
    }

    /**
     * @dataProvider createBlockDataProvider
     */
    public function testCreateBlock($blockType, $blockName, array $blockData, $expectedName, $expectedAnonSuffix = null)
    {
        $expectedData = $blockData + array('type' => $blockType);

        $block = $this->_layout->createBlock($blockType, $blockName, $blockData);

        $this->assertEquals($this->_layout, $block->getLayout());
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
                array(
                    'type' => 'Mage_Core_Block_Text_List',
                    'key1' => 'value1'
                ),
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
     * @dataProvider blockNotExistsDataProvider
     * @expectedException Mage_Core_Exception
     */
    public function testCreateBlockNotExists($name)
    {
        $this->_layout->createBlock($name);
    }

    public function blockNotExistsDataProvider()
    {
        return array(
            array(''),
            array('block_not_exists'),
        );
    }

    /**
     * @param $block
     * @param string $name
     * @param string $parent
     * @param string $alias
     * @param bool $after
     * @param string $sibling
     * @param bool $expected
     * @dataProvider addBlockDataProvider
     */
    public function testAddBlock($block, $name = '', $parent = '', $alias = '', $after = true, $sibling = '',
        $expected = true
    ) {
        $result = $this->_layout->addBlock($block, $name, $parent, $alias, $after, $sibling);
        if ($expected) {
            if (is_string($block)) {
                $this->assertInstanceOf($block, $result);
            } elseif ($block instanceof Mage_Core_Block_Abstract) {
                $this->assertEquals($block, $result);
            }
        }
    }

    public function addBlockDataProvider()
    {
        return array(
            array('Mage_Core_Block_Text', 'child', 'parent', 'alias', true, 'sibling', true),
            array(new Mage_Core_Block_Text, 'child', 'parent', 'alias', true, 'sibling', true),
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testAddBlockInvalidType()
    {
        $this->_layout->addBlock('invalid_name', 'child', 'parent', 'alias', true, 'sibling');
    }

    public function testIsContainer()
    {
        $block = 'block';
        $container = 'container';
        $this->_layout->addBlock('Mage_Core_Block_Text', $block);
        $this->_layout->insertContainer('', $container);
        $this->assertFalse($this->_layout->isContainer($block));
        $this->assertTrue($this->_layout->isContainer($container));
        $this->assertFalse($this->_layout->isContainer('invalid_name'));
    }

    public function testAppendBlock()
    {
        $parentName = 'parent';
        $block1Name = 'block1';
        $block2Name = 'block2';
        $this->_layout->insertContainer('', $parentName);
        $this->_layout->createBlock('Mage_Core_Block_Text', $block1Name);
        $block2 = $this->_layout->createBlock('Mage_Core_Block_Text', $block2Name);
        $this->assertEmpty($this->_layout->getChildNames($parentName));

        $this->_layout->appendBlock($parentName, $block1Name);
        $children = $this->_layout->getChildNames($parentName);
        $this->assertEquals(array($block1Name), $children);
        $this->_layout->appendBlock($parentName, $block2);
        $children = $this->_layout->getChildNames($parentName);
        $this->assertEquals(array($block1Name, $block2Name), $children);
    }

    public function testRenameElement()
    {
        $blockName = 'block';
        $expBlockName = 'block_renamed';
        $containerName = 'container';
        $expContainerName = 'container_renamed';
        $block = $this->_layout->createBlock('Mage_Core_Block_Text', $blockName);
        $this->_layout->insertContainer('', $containerName);

        $this->assertEquals($block, $this->_layout->getBlock($blockName));
        $this->_layout->renameElement($blockName, $expBlockName);
        $this->assertEquals($block, $this->_layout->getBlock($expBlockName));

        $this->_layout->hasElement($containerName);
        $this->_layout->renameElement($containerName, $expContainerName);
        $this->_layout->hasElement($expContainerName);
    }

    /**
     * @covers Mage_Core_Model_Layout::getParentName
     * @covers Mage_Core_Model_Layout::getElementAlias
     */
    public function testGetParentName()
    {
        $parent = 'block1';
        $child = 'block2';
        $alias = 'alias';
        $this->_layout->createBlock('Mage_Core_Block_Text', $parent);
        $this->assertEmpty($this->_layout->getParentName($parent));
        $this->assertEquals($parent, $this->_layout->getElementAlias($parent));

        $this->_layout->addBlock('Mage_Core_Block_Text', $child, $parent, $alias);
        $this->assertEquals($parent, $this->_layout->getParentName($child));
        $this->assertEquals($alias, $this->_layout->getElementAlias($child));
    }

    /**
     * @covers Mage_Core_Model_Layout::addOutputElement
     * @covers Mage_Core_Model_Layout::getOutput
     * @covers Mage_Core_Model_Layout::removeOutputElement
     */
    public function testGetOutput()
    {
        $blockName = 'block_' . __METHOD__;
        $expectedText = "some_text_for_$blockName";

        $block = $this->_layout->addBlock('Mage_Core_Block_Text', $blockName);
        $block->setText($expectedText);

        $this->_layout->addOutputElement($blockName);
        // add the same element twice should not produce output duplicate
        $this->_layout->addOutputElement($blockName);
        $this->assertEquals($expectedText, $this->_layout->getOutput());

        $this->_layout->removeOutputElement($blockName);
        $this->assertEmpty($this->_layout->getOutput());
    }

    public function testGetMessagesBlock()
    {
        $this->assertInstanceOf('Mage_Core_Block_Messages', $this->_layout->getMessagesBlock());
    }

    /**
     * @param string $blockType
     * @param string $expectedClassName
     * @dataProvider getBlockSingletonDataProvider
     */
    public function testGetBlockSingleton($blockType, $expectedClassName)
    {
        $block = $this->_layout->getBlockSingleton($blockType);
        $this->assertInstanceOf($expectedClassName, $block);
        $this->assertSame($block, $this->_layout->getBlockSingleton($blockType));
    }

    public function getBlockSingletonDataProvider()
    {
        return array(
            array('Mage_Core_Block_Text', 'Mage_Core_Block_Text')
        );
    }

    public function testHelper()
    {
        $helper = $this->_layout->helper('Mage_Core_Helper_Data');
        $this->assertInstanceOf('Mage_Core_Helper_Data', $helper);
        $this->assertSame($this->_layout, $helper->getLayout());
    }

    /**
     * @dataProvider findTranslationModuleNameDefaultsDataProvider
     */
    public function testFindTranslationModuleNameDefaults($node, $moduleName)
    {
        $this->markTestIncomplete('Method it self not finished as has commented out logic.');
        $this->assertEquals($moduleName, Mage_Core_Model_Layout::findTranslationModuleName($node));
    }

    public function findTranslationModuleNameDefaultsDataProvider()
    {
        $layout = '<layout>
            <catalogsearch_test>
                <block type="test/test">
                    <block type="child/test"></block>
                </block>
            </catalogsearch_test>
        </layout>';
        $layout = simplexml_load_string($layout, 'Varien_Simplexml_Element');
        $block = $layout->xpath('catalogsearch_test/block/block');
        $block = $block[0];
        return array(
            array(
                simplexml_load_string('<node module="Notexisting_Module">test</node>', 'Varien_Simplexml_Element'),
                'core'
            ),
            array($block, 'core'),
        );
    }
}
