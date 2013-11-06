<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Helper\Product;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $flatFlag = $this->getMock('Magento\Catalog\Model\Product\Flat\Flag', array(), array(), '', false);
        $flatFlag->expects($this->once())->method('loadSelf');

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $objectHelper->getObject('Magento\Catalog\Helper\Product\Flat', array('flatFlag' => $flatFlag));
    }
}
