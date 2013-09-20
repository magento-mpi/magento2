<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf('Magento\Backend\Helper\Data',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Adminhtml\Helper\Data'));
    }
}
