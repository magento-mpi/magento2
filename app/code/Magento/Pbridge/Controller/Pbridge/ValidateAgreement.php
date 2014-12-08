<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Controller\Pbridge;

class ValidateAgreement extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Validate all agreements
     * (terms and conditions are agreed)
     *
     * @return void
     */
    public function execute()
    {
        $result = [];
        $result['success'] = true;
        $agreementsValidator = $this->_objectManager->get('Magento\Checkout\Model\Agreements\AgreementsValidator');
        if (!$agreementsValidator->isValid(array_keys($this->getRequest()->getPost('agreement', [])))) {
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = __(
                'Please agree to all the terms and conditions before placing the order.'
            );
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
        );
    }
}
