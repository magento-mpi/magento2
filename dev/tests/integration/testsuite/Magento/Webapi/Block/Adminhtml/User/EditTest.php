<?php
/**
 * Test for \Magento\Webapi\Block\Adminhtml\User\Edit block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\User;

/**
 * @magentoAppArea adminhtml
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\User\Edit
     */
    protected $_block;

    /**
     * Initialize block.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        $this->_block = $this->_layout->createBlock('Magento\Webapi\Block\Adminhtml\User\Edit');
    }

    /**
     * Test _beforeToHtml method.
     */
    public function testBeforeToHtml()
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete.
        $apiUser = new \Magento\Object();
        $this->_block->setApiUser($apiUser);
        $this->_block->toHtml();
        $this->assertSame($apiUser, $this->_block->getChildBlock('form')->getApiUser());
    }
}
