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
namespace Magento\Adminhtml\Model;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(
            'Magento\Backend\Model\Session',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Adminhtml\Model\Session')
        );
    }
}
