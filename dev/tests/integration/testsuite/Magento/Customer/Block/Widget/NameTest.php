<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Widget;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test \Magento\Customer\Block\Widget\Name
 */
class NameTest extends \PHPUnit_Framework_TestCase
{
    /** @var Name */
    protected $_block;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');
        $this->_block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Widget\Name');
    }

    public function testToHtmlSimpleName()
    {
        /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder */
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
        /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder */
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
