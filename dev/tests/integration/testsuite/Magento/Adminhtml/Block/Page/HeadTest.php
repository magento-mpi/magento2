<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Page;

/**
 * @magentoAppArea adminhtml
 */
class HeadTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Adminhtml\Block\Page\Head',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
                ->createBlock('Magento\Adminhtml\Block\Page\Head')
        );
    }
}
