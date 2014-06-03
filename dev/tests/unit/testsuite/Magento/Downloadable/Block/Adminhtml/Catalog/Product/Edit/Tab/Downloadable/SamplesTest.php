<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

class SamplesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links
     */
    protected $_block;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject(
            'Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples',
            array('urlBuilder' => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false))
        );
    }

    /**
     * Test that getConfig method retrieve \Magento\Framework\Object object
     */
    public function testGetConfig()
    {
        $this->assertInstanceOf('Magento\Framework\Object', $this->_block->getConfig());
    }
}
