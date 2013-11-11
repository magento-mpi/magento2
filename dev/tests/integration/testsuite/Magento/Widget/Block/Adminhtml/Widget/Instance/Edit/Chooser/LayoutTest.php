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


namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

/**
 * @magentoAppArea adminhtml
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $layoutUtility = new \Magento\Core\Utility\Layout($this);
        $appState = $objectManager->get('Magento\App\State');
        $appState->setAreaCode(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $args = array(
            'context' => $objectManager->get('Magento\Core\Block\Template\Context'),
            'layoutProcessorFactory' => $this->getMock('Magento\View\Layout\ProcessorFactory',
                array(), array(), '', false),
            'themesFactory' => $objectManager->get('Magento\Core\Model\Resource\Theme\CollectionFactory'),
            'appState' => $appState,
            'data' => array(
                'name'  => 'page_type',
                'id'    => 'page_types_select',
                'class' => 'page-types-select',
                'title' => 'Page Types Select',
            )
        );
        $this->_block = $this->getMock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout',
            array('_getLayoutProcessor'), $args
        );
        $this->_block
            ->expects($this->any())
            ->method('_getLayoutProcessor')
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
