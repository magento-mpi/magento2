<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\User\Block\Role\Grid;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Block\Role\Grid\User
     */
    protected $_block;

    public function setUp()
    {
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block = $layout->createBlock('Magento\User\Block\Role\Grid\User');
    }

    public function testPreparedCollection()
    {
        $this->_block->toHtml();
        $this->assertInstanceOf('Magento\User\Model\Resource\Role\User\Collection', $this->_block->getCollection());
    }
}
