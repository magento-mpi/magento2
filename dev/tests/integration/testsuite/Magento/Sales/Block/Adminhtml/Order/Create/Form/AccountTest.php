<?php
/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Form;

/**
 * @magentoAppArea adminhtml
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{
   /** @var \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account */
    protected $_accountBlock;

    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $sessionMock = $this->getMockBuilder('Magento\Backend\Model\Session\Quote')
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerId', 'getQuote'])
            ->getMock();
        $sessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote')->load(1);
        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));


        /** @var \Magento\View\LayoutInterface $layout */
        $layout = $this->_objectManager->get('Magento\View\LayoutInterface');
        $this->_accountBlock = $layout->createBlock(
            'Magento\Sales\Block\Adminhtml\Order\Create\Form\Account',
            'address_block' . rand(),
            ['sessionQuote' => $sessionMock]
        );
        parent::setUp();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetForm()
    {
        $expectedFields = ['group_id', 'email'];
        $form = $this->_accountBlock->getForm();
        $this->assertEquals(1, $form->getElements()->count(), "Form has invalid number of fieldsets");
        $fieldset = $form->getElements()[0];

        $this->assertEquals(count($expectedFields), $fieldset->getElements()->count());

        foreach ($fieldset->getElements() as $element) {
            $this->assertTrue(
                in_array($element->getId(), $expectedFields),
                sprintf('Unexpected field "%s" in form.', $element->getId())
            );
        }
    }
}