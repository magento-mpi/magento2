<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

class LinksTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable_Links
     */
    protected $_block;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $urlBuilder = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);
        $attributeFactory = $this->getMock('Magento\Eav\Model\Entity\AttributeFactory', array(), array(), '', false);
        $urlFactory = $this->getMock('Magento\Backend\Model\UrlFactory', array(), array(), '', false);

        $this->_block = $objectManagerHelper->getObject(
            'Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links',
            array(
                'urlBuilder'                                => $urlBuilder,
                'Magento\Eav\Model\Entity\AttributeFactory' => $attributeFactory,
                'Magento\Backend\Model\UrlFactory'          => $urlFactory,
            )
        );
    }

    /**
     * Test that getConfig method retrieve \Magento\Object object
     */
    public function testGetConfig()
    {
        $this->assertInstanceOf('Magento\Object', $this->_block->getConfig());
    }
}
