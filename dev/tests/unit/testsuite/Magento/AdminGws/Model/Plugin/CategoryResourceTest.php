<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model\Plugin;

class CategoryResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Plugin\CategoryResource
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleMock;

    protected function setUp()
    {
        $this->_roleMock = $this->getMock('Magento\AdminGws\Model\Role', array(), array(), '', false);
        $this->_model = new \Magento\AdminGws\Model\Plugin\CategoryResource($this->_roleMock);
    }

    public function testBeforeChangeParentDoesNotCheckCategoryAccessWhenRoleIsNotRestricted()
    {
        $this->_roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(true));
        $this->_roleMock->expects($this->never())
            ->method('hasExclusiveCategoryAccess');
        $this->_model->beforeChangeParent(array());
    }

    /**
     * @param boolean $hasParentPathAccess
     * @param boolean $hasCurrentPathAccess
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage You need more permissions to save this item.
     * @dataProvider beforeChangeParentThrowsExceptionWhenAccessIsRestrictedDataProvider
     */
    public function testBeforeChangeParentThrowsExceptionWhenAccessIsRestricted(
        $hasParentPathAccess,
        $hasCurrentPathAccess
    ) {
        $this->_roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(false));

        $currentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $currentCategory->expects($this->any())
            ->method('getData')
            ->with('path', null)
            ->will($this->returnValue('current/path'));
        $parentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $parentCategory->expects($this->any())
            ->method('getData')
            ->with('path', null)
            ->will($this->returnValue('parent/path'));

        $this->_roleMock->expects($this->any())
            ->method('hasExclusiveCategoryAccess')
            ->will($this->returnValueMap(array(
                array('parent/path', $hasParentPathAccess),
                array('current/path', $hasCurrentPathAccess)
            )));
        $this->_model->beforeChangeParent(array($currentCategory, $parentCategory, null));
    }

    public function beforeChangeParentThrowsExceptionWhenAccessIsRestrictedDataProvider()
    {
        return array(
            array(true, false),
            array(false, true),
            array(false, false),
        );
    }

    public function testBeforeChangeParentDoesNotThrowExceptionWhenUserHasAccessToGivenCategories()
    {
        $this->_roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(false));

        $parentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $parentCategory->expects($this->any())
            ->method('getData')
            ->with('path', null)
            ->will($this->returnValue('parent/path'));
        $currentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $currentCategory->expects($this->any())
            ->method('getData')
            ->with('path', null)
            ->will($this->returnValue('current/path'));

        $this->_roleMock->expects($this->exactly(2))
            ->method('hasExclusiveCategoryAccess')
            ->will($this->returnValueMap(array(
                array('parent/path', true),
                array('current/path', true)
            )));
        $this->_model->beforeChangeParent(array($currentCategory, $parentCategory, null));
    }
}
