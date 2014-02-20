<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class RecurringProfileTest extends \PHPUnit_Framework_TestCase
{

    public function testHandle()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getOrigData', 'setRecurringProfile', 'setIsRecurring', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $product->expects($this->once())->method('getOrigData')->will($this->returnValue('original_data'));
        $product->expects($this->once())->method('setRecurringProfile')->with('original_data');

        $model = $objectHelper->getObject(
            'Magento\RecurringProfile\Controller\\'
             . 'Adminhtml\Product\Initialization\Helper\Plugin\Handler\RecurringProfile'
        );
        $model->handle($product);
    }
}
