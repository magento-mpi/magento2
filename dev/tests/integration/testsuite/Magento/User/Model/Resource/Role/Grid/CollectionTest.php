<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource\Role\Grid;

/**
 * @magentoAppArea adminhtml
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Model\Resource\Role\Grid\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\Resource\Role\Grid\Collection'
        );
    }

    public function testGetItems()
    {
        $expectedResult = array(
            array(
                'role_type' => \Magento\User\Model\Acl\Role\Group::ROLE_TYPE,
                'role_name' => \Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME
            )
        );
        $actualResult = array();
        /** @var \Magento\Reports\Model\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
