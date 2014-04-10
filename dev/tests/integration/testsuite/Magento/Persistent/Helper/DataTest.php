<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Helper;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 * @magentoConfigFixture current_store persistent/options/customer 1
 * @magentoConfigFixture current_store persistent/options/enabled 1
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentHelper;

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

        $this->_persistentHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Helper\Data',
            ['persistentSession' => $this->_persistentSessionHelper]
        );
    }

    public function testGetPersistentName()
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

        $this->assertEquals($translation, $this->_persistentHelper->getPersistentName());
    }
}
