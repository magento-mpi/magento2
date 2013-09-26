<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $layoutUtility = new \Magento\Core\Utility\Layout($this);
        $args = array(
            'context' => \Mage::getSingleton('Magento\Core\Block\Template\Context'),
            'layoutMergeFactory' => $this->getMock('Magento\Core\Model\Layout\MergeFactory',
                array(), array(), '', false),
            'themesFactory' => \Mage::getSingleton('Magento\Core\Model\Resource\Theme\CollectionFactory'),
            'data' => array(
                'name'  => 'page_type',
                'id'    => 'page_types_select',
                'class' => 'page-types-select',
                'title' => 'Page Types Select',
            )
        );
        $this->_block = $this->getMock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout',
            array('_getLayoutMerge'), $args
        );
        $this->_block
            ->expects($this->any())
            ->method('_getLayoutMerge')
            ->will($this->returnCallback(
                function () use ($layoutUtility) {
                    return $layoutUtility->getLayoutUpdateFromFixture(glob(__DIR__ . '/_files/layout/*.xml'));
                }
            ))
        ;
    }

    public function testToHtml()
    {
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/page_types_select.html', $this->_block->toHtml());
    }
}
