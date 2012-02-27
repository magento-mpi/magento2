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

class Mage_Core_Block_AbstractTestAbstract extends Mage_Core_Block_Abstract
{
}

/**
 * @group module:Mage_Core
 */
class Mage_Core_Block_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Block_Abstract
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_Core_Block_AbstractTestAbstract;
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Mage_Core_Controller_Request_Http', $this->_block->getRequest());
    }

    public function testGetParentBlock()
    {
        // need to create blocks through layout
        $parentBlock = $this->_createBlockWithLayout('block1', 'block1', 'Mage_Core_Block_Text');
        $childBlock = $this->_createBlockWithLayout('block2', 'block2');

        $this->assertEmpty($childBlock->getParentBlock());
        $parentBlock->setChild('block2', $childBlock);
        $this->assertSame($parentBlock, $childBlock->getParentBlock());
    }

    public function testSetGetIsAnonymous()
    {
        $this->assertFalse($this->_block->isAnonymous());
        $this->_block->setIsAnonymous(true);
        $this->assertTrue($this->_block->isAnonymous());
    }

    public function testSetGetAnonSuffix()
    {
        $this->assertEquals('', $this->_block->getAnonSuffix());
        $this->_block->setAnonSuffix('suffix');
        $this->assertEquals('suffix', $this->_block->getAnonSuffix());
    }

    public function testSetGetNameInLayout()
    {
        // basic setting/getting
        $this->assertEmpty($this->_block->getNameInLayout());
        $name = uniqid('name');
        $this->_block->setNameInLayout($name);
        $this->assertEquals($name, $this->_block->getNameInLayout());

        // setting second time, along with the layout
        $layout = Mage::app()->getLayout();
        $layout->createBlock('Mage_Core_Block_Template', $name);
        $block = $layout->getBlock($name);
        $this->assertInstanceOf('Mage_Core_Block_Abstract', $block);
        $block->setNameInLayout($name);
        $this->assertInstanceOf('Mage_Core_Block_Abstract', $layout->getBlock($name));
    }

    /**
     * @covers Mage_Core_Block_Abstract::getSortedChildren
     * @covers Mage_Core_Block_Abstract::insert
     */
    public function testGetChildNames()
    {
        // need to create blocks through layout
        $parent = $this->_createBlockWithLayout('parent', 'parent');
        $block1 = $this->_createBlockWithLayout('block1');
        $block2 = $this->_createBlockWithLayout('block2');
        $block3 = $this->_createBlockWithLayout('block3');
        $block4 = $this->_createBlockWithLayout('block4');

        $parent->insert($block1); // add one block
        $parent->insert($block2, 'block1', false); // add second to the 1st position
        $parent->insert($block3, 'block1', false); // add third to the 2nd position
        $parent->insert($block4, 'block3', true); // add fourth block to the 3rd position

        $this->assertEquals(array(
            'block2', 'block3', 'block4', 'block1'
        ), $parent->getChildNames());
    }

    public function testSetAttribute()
    {
        $this->assertEmpty($this->_block->getSomeValue());
        $this->_block->setAttribute('some_value', 'value');
        $this->assertEquals('value', $this->_block->getSomeValue());
    }

    public function testSetGetUnsetChild()
    {
        $parent = $this->_createBlockWithLayout('parent', 'parent');

        // regular block
        $nameOne = uniqid('block.');
        $blockOne = $this->_createBlockWithLayout($nameOne, $nameOne, 'Mage_Core_Block_Template');
        $parent->setChild('block1', $blockOne);
        $this->assertSame($blockOne, $parent->getChildBlock('block1'));

        // block factory name
        $blockTwo = $this->_createBlockWithLayout('parent_block2', 'parent_block2', 'Mage_Core_Block_Template');
        $blockTwo->setChild('block2', $nameOne);
        $this->assertSame($blockOne, $blockTwo->getChildBlock('block2'));

        // anonymous block
        $blockThree = $this->_createBlockWithLayout('', '', 'Mage_Core_Block_Template');
        //$blockThree->setIsAnonymous(true);
        $parent->setChild('block3', $blockThree);
        $this->assertSame($blockThree, $parent->getChildBlock('block3'));

        // unset
        $parent->unsetChild('block3');
        $this->assertNotSame($blockThree, $parent->getChildBlock('block3'));
        $parent->insert($blockOne, '', true, 'block1');
        $this->assertContains($nameOne, $parent->getChildNames());
        $parent->unsetChild('block1');
        $this->assertNotSame($blockOne, $parent->getChildBlock('block1'));
        $this->assertNotContains($nameOne, $parent->getChildNames());
    }

    public function testUnsetCallChild()
    {
        $blockParent = $this->_createBlockWithLayout('parent', 'parent');
        $blockOne = $this->_createBlockWithLayout('block1', 'block1', 'Mage_Core_Block_Template');
        $blockOne->setSomeValue(true);
        $blockParent->setChild('block1', $blockOne);
        $this->assertSame($blockOne, $blockParent->getChildBlock('block1'));
        $blockParent->unsetCallChild('block1', 'getSomeValue', true, array());
        $this->assertNotSame($blockOne, $blockParent->getChildBlock('block1'));
    }

    /**
     * @covers Mage_Core_Block_Abstract::unsetChildren
     * @covers Mage_Core_Block_Abstract::getChildBlock
     */
    public function testUnsetChildren()
    {
        $parent = $this->_createBlockWithLayout('block', 'block');
        $this->assertEquals(array(), $parent->getChildNames());
        $blockOne = $this->_createBlockWithLayout('block1', 'block1', 'Mage_Core_Block_Template');
        $blockTwo = $this->_createBlockWithLayout('block2', 'block2', 'Mage_Core_Block_Template');
        $parent->setChild('block1', $blockOne);
        $parent->setChild('block2', $blockTwo);
        $this->assertSame($blockOne, $parent->getChildBlock('block1'));
        $this->assertSame($blockTwo, $parent->getChildBlock('block2'));
        $parent->unsetChildren();
        $this->assertEquals(array(), $parent->getChildNames());
    }

    /**
     * @covers Mage_Core_Block_Abstract::getChildHtml
     * @covers Mage_Core_Block_Abstract::getChildChildHtml
     */
    public function testGetChildHtml()
    {
        $parent = $this->_createBlockWithLayout('parent', 'parent');
        $blockOne = $this->_createBlockWithLayout('block1', 'block1', 'Mage_Core_Block_Text');
        $blockTwo = $this->_createBlockWithLayout('block2', 'block2', 'Mage_Core_Block_Text');
        $blockOne->setText('one');
        $blockTwo->setText('two');
        $parent->insert($blockTwo, '', false, 'block2'); // make block2 1st
        $parent->insert($blockOne, '', false, 'block1'); // make block1 1st

        $this->assertEquals('one', $parent->getChildHtml('block1'));
        $this->assertEquals('two', $parent->getChildHtml('block2'));

        // sorted will render in the designated order
        $this->assertEquals('onetwo', $parent->getChildHtml('', true, true));

        // getChildChildHtml
        $blockTwo->setChild('block11', $blockOne);
        $this->assertEquals('one', $parent->getChildChildHtml('block2'));
        $this->assertEquals('', $parent->getChildChildHtml(''));
        $this->assertEquals('', $parent->getChildChildHtml('block3'));
    }

    /**
     * @covers Mage_Core_Block_Abstract::insert
     * @see testGetSortedChildren()
     */
    public function testInsert()
    {
        $parent = $this->_createBlockWithLayout('parent', 'parent');

        // invalid block from layout
        $blockZero = $this->_createBlockWithLayout('zero', 'zero', 'Mage_Core_Block_Template');
        $this->assertInstanceOf('Mage_Core_Block_Abstract', $blockZero->insert(uniqid('block.')));

        // anonymous block
        $blockOne = $this->_createBlockWithLayout('', '', 'Mage_Core_Block_Template');
        $parent->insert($blockOne);
        $this->assertContains('parent.child0', $parent->getChildNames());

        // block with alias, to the last position
        $blockTwo = $this->_createBlockWithLayout('block.two', '', 'Mage_Core_Block_Template');
        $parent->insert($blockTwo, '', true, 'block_two');
        $this->assertContains('block.two', $parent->getChildNames());
        $this->assertSame($blockTwo, $parent->getChildBlock('block_two'));

        // unknown sibling, to the 1st position
        $blockThree = $this->_createBlockWithLayout('block.three', '', 'Mage_Core_Block_Template');
        $parent->insert($blockThree, 'wrong_sibling', false, 'block_three');
        $this->assertContains('block.three', $parent->getChildNames());
        $this->assertSame(0, array_search('block.three', $parent->getChildNames()));

        $blockFour = $this->_createBlockWithLayout('block.four', '', 'Mage_Core_Block_Template');
        $parent->insert($blockFour, 'wrong_sibling', true, 'block_four');
        $this->assertContains('block.four', $parent->getChildNames());
        $this->assertSame(3, array_search('block.four', $parent->getChildNames()));
    }

    public function testAddToParentGroup()
    {
        $parent = $this->_createBlockWithLayout('parent', 'parent');
        $block1 = $this->_createBlockWithLayout('block1', 'block1', 'Mage_Core_Block_Template');
        $block2 = $this->_createBlockWithLayout('block2', 'block2', 'Mage_Core_Block_Template');
        $parent->append($block1, 'block1')->append($block2, 'block2');
        $block1->addToParentGroup('group');
        $block2->addToParentGroup('group');
        $group = $parent->getGroupChildNames('group');
        $this->assertContains('block1', $group);
        $this->assertContains('block2', $group);
        $this->assertSame($group[0], 'block1');
        $this->assertSame($group[1], 'block2');
    }

    public function testGetChildData()
    {
        $parent = $this->_createBlockWithLayout('parent', 'parent');
        $block = $this->_createBlockWithLayout('block', 'block', 'Mage_Core_Block_Template');
        $block->setSomeValue('value');
        $parent->setChild('block1', $block);
        $this->assertEquals(
            array('type' => 'Mage_Core_Block_Template', 'some_value' => 'value'),
            $parent->getChildData('block1')
        );
        $this->assertEquals('value', $parent->getChildData('block1', 'some_value'));
        $this->assertNull($parent->getChildData('unknown_block'));
    }

    public function testSetFrameTags()
    {
        $block = new Mage_Core_Block_Text;
        $block->setText('text');

        $block->setFrameTags('p');
        $this->assertEquals('<p>text</p>', $block->toHtml());

        $block->setFrameTags('p class="note"', '/p');
        $this->assertEquals('<p class="note">text</p>', $block->toHtml());

        $block->setFrameTags('non-wellformed tag', 'closing tag');
        $this->assertEquals('<non-wellformed tag>text<closing tag>', $block->toHtml());
    }

    public function testGetUrl()
    {
        $base = 'http://localhost/index.php/';
        $withRoute = "{$base}catalog/product/view/id/10/";
        $this->assertEquals($base, $this->_block->getUrl());
        $this->assertEquals($withRoute, $this->_block->getUrl('catalog/product/view', array('id' => 10)));
    }

    /**
     * @covers Mage_Core_Block_Abstract::getUrlBase64
     * @covers Mage_Core_Block_Abstract::getUrlEncoded
     */
    public function testGetUrlBase64()
    {
        foreach (array('getUrlBase64', 'getUrlEncoded') as $method) {
            $base = 'http://localhost/index.php/';
            $withRoute = "{$base}catalog/product/view/id/10/";

            $encoded = $this->_block->$method();
            $this->assertEquals(Mage::helper('Mage_Core_Helper_Data')->urlDecode($encoded), $base);
            $encoded = $this->_block->$method('catalog/product/view', array('id' => 10));
            $this->assertEquals(Mage::helper('Mage_Core_Helper_Data')->urlDecode($encoded), $withRoute);
        }
    }

    public function testGetSkinUrl()
    {
        $this->assertStringStartsWith('http://localhost/pub/media/skin/frontend/', $this->_block->getSkinUrl());
        $this->assertStringEndsWith('css/styles.css', $this->_block->getSkinUrl('css/styles.css'));
    }

    public function testGetSetMessagesBlock()
    {
        // get one from layout
        $this->_block->setLayout(new Mage_Core_Model_Layout);
        $this->assertInstanceOf('Mage_Core_Block_Messages', $this->_block->getMessagesBlock());

        // set explicitly
        $messages = new Mage_Core_Block_Messages;
        $this->_block->setMessagesBlock($messages);
        $this->assertSame($messages, $this->_block->getMessagesBlock());
    }

    public function testGetHelper()
    {
        $this->_block->setLayout(new Mage_Core_Model_Layout);
        $this->assertInstanceOf('Mage_Core_Block_Text', $this->_block->getHelper('Mage_Core_Block_Text'));
    }

    public function testHelper()
    {
        // without layout
        $this->assertInstanceOf('Mage_Core_Helper_Data', $this->_block->helper('Mage_Core_Helper_Data'));

        // with layout
        $this->_block->setLayout(new Mage_Core_Model_Layout);
        $helper = $this->_block->helper('Mage_Core_Helper_Data');

        try {
            $this->assertInstanceOf('Mage_Core_Helper_Data', $helper);
            $this->assertInstanceOf('Mage_Core_Model_Layout', $helper->getLayout());
            /* Helper is a 'singleton', so assigned layout may affect further helper usage */
            $helper->setLayout(null);
        } catch (Exception $e) {
            $helper->setLayout(null);
            throw $e;
        }
    }

    public function testFormatDate()
    {
        $helper = Mage::helper('Mage_Core_Helper_Data');
        $this->assertEquals($helper->formatDate(), $this->_block->formatDate());
    }

    public function testFormatTime()
    {
        $helper = Mage::helper('Mage_Core_Helper_Data');
        $this->assertEquals($helper->formatTime(), $this->_block->formatTime());
    }

    public function testGetModuleName()
    {
        $this->assertEquals('Mage_Core', $this->_block->getModuleName());
        $this->assertEquals('Mage_Core', $this->_block->getData('module_name'));
    }

    public function testExtractModuleName()
    {
        $this->assertEquals('Mage_Core', Mage_Core_Block_Abstract::extractModuleName('Mage_Core_Block_Abstract'));
    }

    public function test__()
    {
        $str = uniqid();
        $this->assertEquals($str, $this->_block->__($str));
    }

    /**
     * @dataProvider escapeHtmlDataProvider
     */
    public function testEscapeHtml($data, $expected)
    {
        $actual = $this->_block->escapeHtml($data);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function escapeHtmlDataProvider()
    {
        return array(
            'array data' => array(
                'data' => array('one', '<two>three</two>'),
                'expected' => array('one', '&lt;two&gt;three&lt;/two&gt;')
            ),
            'string data conversion' => array(
                'data' => '<two>three</two>',
                'expected' => '&lt;two&gt;three&lt;/two&gt;'
            ),
            'string data no conversion' => array(
                'data' => 'one',
                'expected' => 'one'
            )
        );
    }

    public function testStripTags()
    {
        $str = '<p>text</p>';
        $this->assertEquals('text', $this->_block->stripTags($str));
    }

    public function testEscapeUrl()
    {
        $url = 'http://example.com/?wsdl=1';
        $this->assertEquals($url, $this->_block->escapeUrl($url));
    }

    public function testJsQuoteEscape()
    {
        $script = "var s = 'text';";
        $this->assertEquals('var s = \\\'text\\\';', $this->_block->jsQuoteEscape($script));
    }

    public function testGetCacheKeyInfo()
    {
        $name = uniqid('block.');
        $block = new Mage_Core_Block_Text;
        $block->setNameInLayout($name);
        $this->assertEquals(array($name), $block->getCacheKeyInfo());
    }

    public function testGetCacheKey()
    {
        $name = uniqid('block.');
        $block = new Mage_Core_Block_Text;
        $block->setNameInLayout($name);
        $key = $block->getCacheKey();
        $this->assertNotEmpty($key);
        $this->assertNotEquals('key', $key);
        $this->assertNotEquals($name, $key);

        $block->setCacheKey('key');
        $this->assertEquals('key', $block->getCacheKey());
    }

    public function testGetCacheTags()
    {
        $this->assertContains(Mage_Core_Block_Abstract::CACHE_GROUP, $this->_block->getCacheTags());

        $this->_block->setCacheTags(array('one', 'two'));
        $tags = $this->_block->getCacheTags();
        $this->assertContains(Mage_Core_Block_Abstract::CACHE_GROUP, $tags);
        $this->assertContains('one', $tags);
        $this->assertContains('two', $tags);
    }

    public function testGetCacheLifetime()
    {
        $this->assertNull($this->_block->getCacheLifetime());
        $this->_block->setCacheLifetime(1800);
        $this->assertEquals(1800, $this->_block->getCacheLifetime());
    }

    /**
     * App isolation is enabled, because config options object is affected
     *
     * @magentoAppIsolation enabled
     */
    public function testGetVar()
    {
        Mage::getConfig()->getOptions()->setDesignDir(dirname(__DIR__) . '/Model/_files/design');
        Mage::getDesign()->setDesignTheme('test/default/default');
        $this->assertEquals('Core Value1', $this->_block->getVar('var1'));
        $this->assertEquals('value1', $this->_block->getVar('var1', 'Namespace_Module'));
        $this->_block->setModuleName('Namespace_Module');
        $this->assertEquals('value1', $this->_block->getVar('var1'));
        $this->assertEquals(false, $this->_block->getVar('unknown_var'));
    }

    /**
     * Create <N> sample blocks
     *
     * @param int $qty
     * @param bool $withLayout
     * @param string $className
     * @return array
     */
    protected function _createSampleBlocks($qty, $withLayout = true, $className = 'Mage_Core_Block_Template')
    {
        $blocks = array(); $names = array();
        $layout = false;
        if ($withLayout) {
            $layout = new Mage_Core_Model_Layout;
        }
        for ($i = 0; $i < $qty; $i++) {
            $name = uniqid('block.');
            if ($layout) {
                $block = $layout->createBlock($className, $name);
                $layout->insertBlock('', $name, $name);
            } else {
                $block = new $className;
                $block->setNameInLayout($name);
            }
            $blocks[] = $block;
            $names[] = $name;
        }
        return array($blocks, $names);
    }

    protected function _createBlockWithLayout($name = 'block', $alias = null,
        $type = 'Mage_Core_Block_AbstractTestAbstract'
    ) {
        $layout = Mage::app()->getLayout();
        $block = $layout->createBlock($type, $name);
        if ($alias) {
            $layout->insertBlock('', $name, $alias);
        }
        return $block;
    }
}
