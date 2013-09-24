<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Rating\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Adminhtml\Block\Rating\Edit\Tab\Form',
            \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit\Tab\Form')
        );
    }
}
