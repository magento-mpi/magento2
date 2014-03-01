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
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleMock;

    protected function setUp()
    {
        $this->roleMock = $this->getMock('Magento\AdminGws\Model\Role', array(), array(), '', false);
        $this->model = new \Magento\AdminGws\Model\Plugin\CategoryResource($this->roleMock);
    }

    public function testBeforeChangeParentDoesNotCheckCategoryAccessWhenRoleIsNotRestricted()
    {
        $subjectMock = $this->getMock('Magento\Catalog\Model\Resource\Category', array(), array(), '', false);
        $currentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $parentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $this->roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(true));
        $this->roleMock->expects($this->never())
            ->method('hasExclusiveCategoryAccess');
        $this->model->beforeChangeParent($subjectMock, $currentCategory, $parentCategory);
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
        $this->roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(false));

        $subjectMock = $this->getMock('Magento\Catalog\Model\Resource\Category', array(), array(), '', false);
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

        $this->roleMock->expects($this->any())
            ->method('hasExclusiveCategoryAccess')
            ->will($this->returnValueMap(array(
                array('parent/path', $hasParentPathAccess),
                array('current/path', $hasCurrentPathAccess)
            )));
        $this->model->beforeChangeParent($subjectMock, $currentCategory, $parentCategory, null);
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
        $this->roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(false));

        $subjectMock = $this->getMock('Magento\Catalog\Model\Resource\Category', array(), array(), '', false);
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

        $this->roleMock->expects($this->exactly(2))
            ->method('hasExclusiveCategoryAccess')
            ->will($this->returnValueMap(array(
                array('parent/path', true),
                array('current/path', true)
            )));
        $this->model->beforeChangeParent($subjectMock, $currentCategory, $parentCategory, null);
    }
}
