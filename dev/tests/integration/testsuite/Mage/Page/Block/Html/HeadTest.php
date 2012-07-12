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

class Mage_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Head
     */
    private $_block = null;

    public static function setUpBeforeClass()
    {
        Mage::getDesign()->setDesignTheme('default/default/default', 'frontend');
    }

    protected function setUp()
    {
        $this->_block = new Mage_Page_Block_Html_Head;
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testAddCss()
    {
        $this->assertEmpty($this->_block->getItems());
        $this->_block->addCss('test.css');
        $this->assertEquals(array('css/test.css' => array(
                'type'   => 'css',
                'name'   => 'test.css',
                'params' => 'rel="stylesheet" type="text/css" media="all"',
                'if'     => null,
                'cond'   => null,
            )), $this->_block->getItems()
        );
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testAddCssException()
    {
        $this->_block->addCss('');
    }

    public function testGetCssJsHtml()
    {
        $this->_block->addJs('zero.js', '', null, 'nonexisting_condition')
            ->addJs('varien/js.js')
            ->addJs('Mage_Bundle::bundle.js')
            ->addCss('tiny_mce/themes/advanced/skins/default/ui.css')
            ->addCss('css/styles.css', '   media="print" ')
            ->addRss('RSS Feed', 'http://example.com/feed.xml')
            ->addLinkRel('next', 'http://example.com/page1.html')
            ->addJs('varien/form.js', '', 'lt IE 7')
        ;
        $this->assertEquals(
            '<script type="text/javascript" src="http://localhost/pub/js/varien/js.js"></script>' . "\n"
            . '<script type="text/javascript" '
            . 'src="http://localhost/pub/media/skin/frontend/default/default/default/en_US/Mage_Bundle/bundle.js">'
            . '</script>' . "\n"
            . '<link rel="stylesheet" type="text/css" media="all"'
            . ' href="http://localhost/pub/js/tiny_mce/themes/advanced/skins/default/ui.css" />' . "\n"
            . '<link rel="stylesheet" type="text/css" media="print" '
                . 'href="http://localhost/pub/media/skin/frontend/default/default/default/en_US/css/styles.css" />'
                . "\n"
            . '<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="http://example.com/feed.xml" />'
                . "\n"
            . '<link rel="next" href="http://example.com/page1.html" />' . "\n"
            . '<!--[if lt IE 7]>' . "\n"
            . '<script type="text/javascript" src="http://localhost/pub/js/varien/form.js"></script>' . "\n"
            . '<![endif]-->' . "\n",
            $this->_block->getCssJsHtml()
        );
    }

    /**
     * Test getRobots
     *
     * @magentoConfigFixture default_store design/search_engine_robots/default_robots INDEX,NOFOLLOW
     */
    public function testGetRobots()
    {
        $this->assertEquals('INDEX,NOFOLLOW', $this->_block->getRobots());
    }
}
