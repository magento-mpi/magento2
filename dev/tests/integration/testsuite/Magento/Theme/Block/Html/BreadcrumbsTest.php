<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

class BreadcrumbsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Html\Breadcrumbs
     */
    protected $_block;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\State')
            ->setAreaCode('frontend');
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Theme\Block\Html\Breadcrumbs'
        );
    }

    public function testAddCrumb()
    {
        $this->assertEmpty($this->_block->toHtml());
        $info = array('label' => 'test label', 'title' => 'test title', 'link' => 'test link');
        $this->_block->addCrumb('test', $info);
        $html = $this->_block->toHtml();
        $this->assertContains('test label', $html);
        $this->assertContains('test title', $html);
        $this->assertContains('test link', $html);
    }

    public function testGetCacheKeyInfo()
    {
        $crumbs = array('test' => array('label' => 'test label', 'title' => 'test title', 'link' => 'test link'));
        foreach ($crumbs as $crumbName => &$crumb) {
            $this->_block->addCrumb($crumbName, $crumb);
            $crumb += array('first' => null, 'last' => null, 'readonly' => null);
        }

        $cacheKeyInfo = $this->_block->getCacheKeyInfo();
        $crumbsFromCacheKey = unserialize(base64_decode($cacheKeyInfo['crumbs']));
        $this->assertEquals($crumbs, $crumbsFromCacheKey);
    }
}
