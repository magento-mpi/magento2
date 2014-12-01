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

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Persistent\Helper\Session $persistentSessionHelper */
        $this->_persistentSessionHelper = $this->_objectManager->create('Magento\Persistent\Helper\Session');

        $this->_customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');

        $this->_block = $this->_objectManager->create('Magento\Persistent\Block\Header\Additional');
    }

    /**
     * @magentoConfigFixture current_store persistent/options/customer 1
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_default 1
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        $this->_customerSession->loginById(1);
        /** @var \Magento\Customer\Helper\View $customerViewHelper */
        $customerViewHelper = $this->_objectManager->create(
            'Magento\Customer\Helper\View'
        );
        $customerAccountService = $this->_objectManager->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
        /** @var \Magento\Framework\Escaper $escaper */
        $escaper = $this->_objectManager->create(
            'Magento\Framework\Escaper'
        );
        $persistentName = $escaper->escapeHtml(
            $customerViewHelper->getCustomerName(
                $customerAccountService->getCustomer(
                    $this->_persistentSessionHelper->getSession()->getCustomerId()
                )
            )
        );

        $translation = __('(Not %1?)', $persistentName);

        $this->assertStringMatchesFormat(
            '%A<span>%A<a%Ahref="' . $this->_block->getHref() . '"%A>' . $translation . '</a>%A</span>%A',
            $this->_block->toHtml()
        );
        $this->_customerSession->logout();
    }
}
