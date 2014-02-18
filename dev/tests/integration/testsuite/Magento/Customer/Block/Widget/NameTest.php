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

class NameTest extends \PHPUnit_Framework_TestCase
{
    /** @var Name */
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
            ->createBlock('Magento\Customer\Block\Widget\Name');
    }

    public function testToHtmlSimpleName()
    {
        /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerService */
        $customerBuilder = Bootstrap::getObjectManager()->get('Magento\Customer\Service\V1\Dto\CustomerBuilder');
        $customerBuilder->setFirstname('Jane');
        $customerBuilder->setLastname('Doe');
        $this->_block->setObject($customerBuilder->create());
        $html = $this->_block->toHtml();
        $this->assertContains('title="First Name"', $html);
        $this->assertContains('value="Jane"', $html);
        $this->assertContains('title="Last Name"', $html);
        $this->assertContains('value="Doe"', $html);
    }

    public function testToHtmlFancyName()
    {
        /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerService */
        $customerBuilder = Bootstrap::getObjectManager()->get('Magento\Customer\Service\V1\Dto\CustomerBuilder');
        $customerBuilder->setPrefix('Dr.')
            ->setFirstname('Jane')
            ->setMiddlename('Roe')
            ->setLastname('Doe')
            ->setSuffix('Ph.D.');
        $this->_block->setObject($customerBuilder->create());
        $html = $this->_block->toHtml();
        $this->assertContains('title="First Name"', $html);
        $this->assertContains('value="Jane"', $html);
        $this->assertContains('title="Last Name"', $html);
        $this->assertContains('value="Doe"', $html);
        $this->assertContains('title="Middle Name/Initial"', $html);
        $this->assertContains('value="Roe"', $html);
        $this->assertContains('title="Prefix"', $html);
        $this->assertContains('value="Dr."', $html);
        $this->assertContains('title="Suffix"', $html);
        $this->assertContains('value="Ph.D."', $html);
    }
}
