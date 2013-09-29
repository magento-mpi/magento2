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

class SamplesTest
    extends \PHPUnit_Framework_TestCase
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
            array(
                'urlBuilder' => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false)
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
