<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locate all payment methods in the system and verify declaration of their blocks
 */
namespace Magento\Test\Integrity\Magento\Payment;

class MethodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $methodClass
     * @param string $code
     * @dataProvider paymentMethodDataProvider
     * @magentoAppArea frontend
     * @throws \Exception on various assertion failures
     */
    public function testPaymentMethod($code, $methodClass)
    {
        /** @var $blockFactory \Magento\View\Element\BlockFactory */
        $blockFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\Element\BlockFactory');
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
        /** @var $model \Magento\Payment\Model\Method\AbstractMethod */
        if (empty($methodClass)) {
            /**
             * Note that $code is not whatever the payment method getCode() returns
             */
            $this->fail("Model of '{$code}' payment method is not found."); // prevent fatal error
        }
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($methodClass);
        $this->assertNotEmpty($model->getTitle());
        foreach (array($model->getFormBlockType(), $model->getInfoBlockType()) as $blockClass) {
            $message = "Block class: {$blockClass}";
            /** @var $block \Magento\View\Block\Template */
            $block = $blockFactory->createBlock($blockClass);
            $block->setArea('frontend');
            $this->assertFileExists($block->getTemplateFile(), $message);
            if ($model->canUseInternal()) {
                try {
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                        ->get('Magento\Core\Model\StoreManagerInterface')
                        ->getStore()
                        ->setId(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID);
                    $block->setArea('adminhtml');
                    $this->assertFileExists($block->getTemplateFile(), $message);
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                        ->get('Magento\Core\Model\StoreManagerInterface')
                        ->getStore()
                        ->setId($storeId);
                } catch (\Exception $e) {
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                        ->get('Magento\Core\Model\StoreManagerInterface')
                        ->getStore()
                        ->setId($storeId);
                    throw $e;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        /** @var $helper \Magento\Payment\Helper\Data */
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Payment\Helper\Data');
        $result = array();
        foreach ($helper->getPaymentMethods() as $code => $method) {
            $result[] = array($code, $method['model']);
        }
        return $result;
    }
}
