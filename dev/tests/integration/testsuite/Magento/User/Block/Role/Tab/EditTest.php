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
namespace Magento\User\Block\Role\Tab;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Block\Role\Tab\Edit
     */
    protected $_block;

    protected function setUp()
    {
        $roleAdmin = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\Role');
        $roleAdmin->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Controller\Request\Http')
            ->setParam('rid', $roleAdmin->getId());

        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Block\Role\Tab\Edit');
    }

    public function testConstructor()
    {
        $this->assertNotEmpty($this->_block->getSelectedResources());
        $this->assertContains(
            'Magento_Adminhtml::all',
            $this->_block->getSelectedResources()
        );
    }

    public function testGetTree()
    {
        $encodedTree = $this->_block->getTree();
        $this->assertNotEmpty($encodedTree);
    }
}
