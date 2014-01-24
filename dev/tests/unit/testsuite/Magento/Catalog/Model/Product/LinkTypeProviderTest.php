<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

class LinkTypeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinkTypeProvider
     */
    protected $model;

    /**
     * @var array
     */
    protected $linkTypes;

    protected function setUp()
    {
        $this->linkTypes = array('link1', 'link2', 'link3');
        $this->model = new LinkTypeProvider($this->linkTypes);
    }

    /**
     * @covers Magento\Catalog\Model\Product\LinkTypeProvider::getLinkTypes
     */
    public function testGetLinkTypes()
    {
        $this->assertEquals($this->linkTypes, $this->model->getLinkTypes());
    }
}
