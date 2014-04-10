<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Persistent\Block\Header;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 */
class AdditionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Persistent\Block\Header\Additional
     */
    protected $_block;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    public function setUp()
    {
        /** @var \Magento\Persistent\Model\Session $persistentSession */
        $persistentSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Model\Session'
        );
        $persistentSession->loadByCustomerId(1);

        $this->_persistentSessionHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Helper\Session'
        );
        $this->_persistentSessionHelper->setSession($persistentSession);

        /** @var \Magento\Persistent\Helper\Data $persistentHelper */
        $persistentHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Helper\Data',
            ['persistentSession' => $this->_persistentSessionHelper]
        );

        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Block\Header\Additional',
            ['persistentHelper' => $persistentHelper]
        );
    }

    /**
     * @magentoConfigFixture current_store persistent/options/customer 1
     * @magentoConfigFixture current_store persistent/options/enabled 1
     */
    public function testToHtml()
    {
        /** @var \Magento\Customer\Helper\View $customerViewHelper */
        $customerViewHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Helper\View'
        );

        /** @var \Magento\Escaper $escaper */
        $escaper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Escaper'
        );
        $translation = __('(Not %1?)',
            $escaper->escapeHtml(
                $customerViewHelper->getCustomerName($this->_persistentSessionHelper->getCustomerDataObject())
            )
        );

        $this->assertStringMatchesFormat(
            '%A<span>%A<a%Ahref="' . $this->_block->getHref() . '"%A>' . $translation . '</a>%A</span>%A',
            $this->_block->toHtml()
        );
    }
}
