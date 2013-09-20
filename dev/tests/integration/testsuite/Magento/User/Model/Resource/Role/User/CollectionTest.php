<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Role user collection test
 * @magentoAppArea adminhtml
 */
namespace Magento\User\Model\Resource\Role\User;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Model\Resource\Role\User\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = \Mage::getResourceModel('Magento\User\Model\Resource\Role\User\Collection');
    }

    public function testSelectQueryInitialized()
    {
        $this->assertContains('user_id > 0', $this->_collection->getSelect()->__toString());
    }
}
