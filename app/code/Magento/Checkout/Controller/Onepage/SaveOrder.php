<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Onepage;

class SaveOrder extends \Magento\Checkout\Controller\Onepage
{
    /**
     * Create order action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            $agreementsValidator = $this->_objectManager->get('Magento\Checkout\Model\Agreements\AgreementsValidator');
            if (!$agreementsValidator->isValid(array_keys($this->getRequest()->getPost('agreement', array())))) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = __(
                    'Please agree to all the terms and conditions before placing the order.'
                );
                $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
                );
                return;
            }

            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = array(
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL
                );
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }

            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error'] = false;
        } catch (\Magento\Payment\Model\Info\Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array('name' => 'payment-method', 'html' => $this->_getPaymentMethodsHtml());
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_objectManager->get(
                'Magento\Checkout\Helper\Data'
            )->sendPaymentFailedEmail(
                $this->getOnepage()->getQuote(),
                $e->getMessage()
            );
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();
            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->{$updateSectionFunction}()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_objectManager->get(
                'Magento\Checkout\Helper\Data'
            )->sendPaymentFailedEmail(
                $this->getOnepage()->getQuote(),
                $e->getMessage()
            );
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = __('Something went wrong processing your order. Please try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
        );
    }
}
