<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Page
 */
class Mage_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Head
     */
    private $_block = null;

    public static function setUpBeforeClass()
    {
        Mage::getDesign()->setArea('frontend')
            ->setPackageName('default')
            ->setTheme('default');
    }

    protected function setUp()
    {
        $this->_block = new Mage_Page_Block_Html_Head;
    }

    public function testAddItem()
    {
        $this->assertEmpty($this->_block->getItems());
        $this->_block->addItem('skin_css', 'test.css');
        $this->assertEquals(array('skin_css/test.css' => array(
                'type'   => 'skin_css',
                'name'   => 'test.css',
                'params' => 'media="all"',
                'if'     => null,
                'cond'   => null,
            )), $this->_block->getItems()
        );
    }

    /**
     * @expectedException Exception
     */
    public function testAddItemException()
    {
        $this->_block->addItem('skin_css', '');
    }

    public function testGetCssJsHtml()
    {
        $this->_block->addItem('js', 'zero.js', null, null, 'nonexisting_condition')
            ->addItem('js', 'one.js')
            ->addItem('skin_js', 'js/bundle.js')
            ->addItem('js_css', 'three.css')
            ->addItem('skin_css', 'css/styles.css')
            ->addItem('rss', 'http://example.com/feed.xml')
            ->addItem('link_rel', 'http://example.com/page1.html', '   rel="next" ')
            ->addItem('js', 'conditional.js', null, 'ie6')
        ;
        $package = Mage::getDesign()->getPackageName();
        $this->assertEquals(
            '<script type="text/javascript" src="http://localhost/js/one.js"></script>' . "\n"
            . '<script type="text/javascript" '
            . 'src="http://localhost/media/skin/frontend/' . $package . '/default/default/en_US/js/bundle.js">'
            . '</script>' . "\n"
            . '<link media="all" rel="stylesheet" type="text/css" href="http://localhost/js/three.css" />' . "\n"
            . '<link media="all" rel="stylesheet" type="text/css" '
                . 'href="http://localhost/media/skin/frontend/' . $package . '/default/default/en_US/css/styles.css" />'
                . "\n"
            . '<link rel="alternate" type="application/rss+xml" href="http://example.com/feed.xml" />' . "\n"
            . '<link rel="next" href="http://example.com/page1.html" />' . "\n"
            . '<!--[if ie6]>' . "\n"
            . '<script type="text/javascript" src="http://localhost/js/conditional.js"></script>' . "\n"
            . '<![endif]-->' . "\n",
            $this->_block->getCssJsHtml()
        );
    }

    /**
     * @expectedException Exception
     */
    public function testGetCssJsHtmlException()
    {
        $this->_block->addItem('unknown_type', 'test.css')->getCssJsHtml();
    }
}
