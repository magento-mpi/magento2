<?php
/**
 * Test class for /Magento/UrlRewrite/Model/UrlRewrite
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManager;
use Magento\UrlRewrite\Model\UrlRewrite as UrlRewrite;

class UrlRewriteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewrite
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionMock;

    public function setUp()
    {

        $resourceMethods = ['getIdFieldName', 'loadByRequestPath', 'load',];
        $this->resourceMock = $this->getMockForAbstractClass('\Magento\Framework\Model\Resource\AbstractResource',
            [], '', false, true, true, $resourceMethods

        );

        $collectionMethods = ['load', 'addTagsFilter', 'getSize'];
        $this->collectionMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Model\Resource\Db\Collection\AbstractCollection',
            [], '', false, true, true, $collectionMethods
        );

        $this->model = (new ObjectManager($this))->getObject('\Magento\UrlRewrite\Model\UrlRewrite',
        [
            'resource' => $this->resourceMock,
            'resourceCollection' => $this->collectionMock,
        ]
        );
    }

    public function testLoadByRequestPath()
    {
        $path = 'path';

        $this->resourceMock->expects($this->once())
            ->method('loadByRequestPath')
            ->with($this->model, $path);

        $this->model->loadByRequestPath($path);

    }

    public function testLoadByIdPath()
    {
        $path = 'path';

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->model, $path, UrlRewrite::PATH_FIELD);

        $this->model->loadByIdPath($path);
    }

    public function testHasOption()
    {
        $searchOption = 'option2';
        $options='option1,' . $searchOption . ',option3';
        $this->assertTrue($this->model->setOptions($options)->hasOption('option2'));
    }

    public function testRewrite()
    {

    }

    public function testGetStoreId()
    {
        $id = 42;
        $this->assertEquals($id, $this->model->setStoreId($id)->getStoreId());
    }

} 