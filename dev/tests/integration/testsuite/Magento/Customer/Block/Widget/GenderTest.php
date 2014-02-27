<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Widget;
use Magento\TestFramework\Helper\Bootstrap;

class GenderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Gender */
    protected $_block;

    /**
     * Test initialization and set up. Create the Gender block.
     * @return void
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');
        $this->_block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Widget\Gender');
    }

    /**
     * Test the Gender::getGenderOptions() method.
     * @return void
     */
    public function testGetGenderOptions()
    {
        $options = $this->_block->getGenderOptions();
        $this->assertInternalType('array', $options);
        $this->assertNotEmpty($options);
        $this->assertContainsOnlyInstancesOf('Magento\Customer\Service\V1\Dto\Eav\Option', $options);
    }

    /**
     * Test the Gender::toHtml() method.
     * @return void
     */
    public function testToHtml()
    {
        $html = $this->_block->toHtml();
        $this->assertContains('<span>Gender</span>', $html);
        $this->assertContains('<option value="1">Male</option>', $html);
        $this->assertContains('<option value="2">Female</option>', $html);
    }
}
