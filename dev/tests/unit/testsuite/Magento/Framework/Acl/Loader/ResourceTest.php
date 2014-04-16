<?php
/**
 * Test for \Magento\Framework\Acl\Loader\Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Acl\Loader;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for \Magento\Framework\Acl\Loader\Resource::populateAcl
     */
    public function testPopulateAclOnValidObjects()
    {
        /** @var $aclResource \Magento\Framework\Acl\Resource */
        $aclResource = $this->getMock('Magento\Framework\Acl\Resource', array(), array(), '', false);

        /** @var $acl \Magento\Framework\Acl */
        $acl = $this->getMock('Magento\Framework\Acl', array('addResource'), array(), '', false);
        $acl->expects($this->exactly(2))->method('addResource');
        $acl->expects($this->at(0))->method('addResource')->with($aclResource, null)->will($this->returnSelf());
        $acl->expects(
            $this->at(1)
        )->method(
            'addResource'
        )->with(
            $aclResource,
            $aclResource
        )->will(
            $this->returnSelf()
        );

        $factoryObject = $this->getMock('Magento\Framework\Acl\ResourceFactory', array('createResource'), array(), '', false);
        $factoryObject->expects($this->any())->method('createResource')->will($this->returnValue($aclResource));

        /** @var $resourceProvider \Magento\Framework\Acl\Resource\ProviderInterface */
        $resourceProvider = $this->getMock('Magento\Framework\Acl\Resource\ProviderInterface');
        $resourceProvider->expects(
            $this->once()
        )->method(
            'getAclResources'
        )->will(
            $this->returnValue(
                array(
                    array(
                        'id' => 'parent_resource::id',
                        'title' => 'Parent Resource Title',
                        'sortOrder' => 10,
                        'children' => array(
                            array(
                                'id' => 'child_resource::id',
                                'title' => 'Child Resource Title',
                                'sortOrder' => 10,
                                'children' => array()
                            )
                        )
                    )
                )
            )
        );

        /** @var $loaderResource \Magento\Framework\Acl\Loader\Resource */
        $loaderResource = new \Magento\Framework\Acl\Loader\Resource($resourceProvider, $factoryObject);

        $loaderResource->populateAcl($acl);
    }
}
