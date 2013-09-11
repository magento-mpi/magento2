<?php
/**
 * Abstract test case for Webapi forms. It was introduced to avoid copy-paste in form tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Webapi_Block_Adminhtml_FormTestAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Form class must be defined in children.
     *
     * @var string
     */
    protected $_formClass = '';

    /**
     * @var \Magento\Webapi\Block\Adminhtml\User\Edit\Form
     */
    protected $_block;

    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Backend\Model\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Core\Model\BlockFactory
     */
    protected $_blockFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_urlBuilder = $this->getMockBuilder('Magento\Backend\Model\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_layout = $this->_objectManager->get('Magento\Core\Model\Layout');
        $this->_blockFactory = $this->_objectManager->get('Magento\Core\Model\BlockFactory');
        $this->_block = $this->_blockFactory->createBlock($this->_formClass, array(
            'context' => Mage::getModel(
                '\Magento\Backend\Block\Template\Context',
                array('urlBuilder' => $this->_urlBuilder)
            )
        ));
        $this->_layout->addBlock($this->_block);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('\Magento\Core\Model\Layout');
        unset($this->_objectManager, $this->_urlBuilder, $this->_layout, $this->_blockFactory, $this->_block);
    }

    /**
     * Test _prepareForm method.
     */
    public function testPrepareForm()
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete.
        $this->assertEmpty($this->_block->getForm());

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/save', array())
            ->will($this->returnValue('action_url'));
        $this->_block->toHtml();

        $form = $this->_block->getForm();
        $this->assertInstanceOf('\Magento\Data\Form', $form);
        $this->assertTrue($form->getUseContainer());
        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $this->assertEquals('action_url', $form->getAction());
    }
}
