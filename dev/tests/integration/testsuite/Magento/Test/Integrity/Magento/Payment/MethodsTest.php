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
     * List of payment method models, which need to be prepared before processing
     * @var array
     */
    protected $_methodsNeedTobePrepared = ['substitution'];

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
        $blockFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\Element\BlockFactory'
        );
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\StoreManagerInterface'
        )->getStore()->getId();
        /** @var $model \Magento\Payment\Model\MethodInterface */
        if (empty($methodClass)) {
            /**
             * Note that $code is not whatever the payment method getCode() returns
             */
            $this->fail("Model of '{$code}' payment method is not found."); // prevent fatal error
        }
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($methodClass);
        $this->_prepareModel($code, $model);
        $this->assertNotEmpty($model->getTitle());
        foreach (array($model->getFormBlockType(), $model->getInfoBlockType()) as $blockClass) {
            $message = "Block class: {$blockClass}";
            /** @var $block \Magento\View\Element\Template */
            $block = $blockFactory->createBlock($blockClass);
            $block->setArea('frontend');
            $this->assertFileExists($block->getTemplateFile(), $message);
            if ($model->canUseInternal()) {
                try {
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                        'Magento\Core\Model\StoreManagerInterface'
                    )->getStore()->setId(
                        \Magento\Core\Model\Store::DEFAULT_STORE_ID
                    );
                    $block->setArea('adminhtml');
                    $this->assertFileExists($block->getTemplateFile(), $message);
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                        'Magento\Core\Model\StoreManagerInterface'
                    )->getStore()->setId(
                        $storeId
                    );
                } catch (\Exception $e) {
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                        'Magento\Core\Model\StoreManagerInterface'
                    )->getStore()->setId(
                        $storeId
                    );
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

    /**
     * Prepares method model with data which is needed for its business logic
     *
     * @param string $code
     * @param \Magento\Payment\Model\MethodInterface $methodModel
     */
    private function _prepareModel($code, $methodModel)
    {
        if (in_array($code, $this->_methodsNeedTobePrepared)) {
            $prepareMethod = 'prepare' . ucfirst(strtolower($code));
            $this->$prepareMethod($methodModel);
        }
    }

    /**
     * @param \Magento\Payment\Model\MethodInterface $methodModel
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function prepareSubstitution($methodModel)
    {
        $paymentInfo = $this->getMockBuilder(
            'Magento\Payment\Model\Info'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $paymentInfo->expects(
            $this->any()
        )->method(
            'getAdditionalInformation'
        )->will(
            $this->returnValue('Additional data mock')
        );
        $methodModel->setInfoInstance($paymentInfo);
    }
}
