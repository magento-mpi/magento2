<?php
/**
 * Magento Saas Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Custom renderer for PayPal EnterBoarding button
 */
class Saas_Paypal_Block_Adminhtml_System_Config_BoardingStatus
    extends Mage_Backend_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'Saas_Paypal::system/config/boarding_status.phtml';

    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $data = $element->getOriginalData();

        /** @var $helper Saas_Paypal_Helper_Data */
        $helper = Mage::helper('Saas_Paypal_Helper_Data');
        $accountData = Mage::getStoreConfig($data['account_id_config_path']);

        if ($accountData) {
            $accountLabel = $helper->__($data['account_id_label']);
        } else {
            $accountLabel = $helper->__($data['account_label']);
            $accountData  = Mage::getStoreConfig($data['account_config_path']);
        }

        $this->addData(array(
            'button_label'   => $helper->__($data['button_label']),
            'account_label'  => $accountLabel,
            'status_label'   => $helper->__($data['status_label']),
            'payment_method' => $data['payment_method'],
            'create_link'    => $data['create_link'],
            'status'         => Mage::getStoreConfig($data['status_config_path']),
            'account'        => $accountData,
            'html_id'        => $element->getId(),
        ));
        return $this->toHtml();
    }

    /**
     * Get URL for token creation (do EnterBoarding request)
     *
     * @return string
     */
    public function getEnterBoardingUrl()
    {
        return $this->getUrl('*/onboarding/enter');
    }

    /**
     * Get status label for displaying
     *
     * @return string
     */
    public function getStatusName()
    {
        /** @var $helper Saas_Paypal_Helper_Data */
        $helper = Mage::helper('Saas_Paypal_Helper_Data');
        $statusLabels = array(
            Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_DISABLED  => $helper->__('Disabled'),
            Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE    => $helper->__('Active'),
        );

        $status = isset($statusLabels[$this->getStatus()])
            ? $this->getStatus()
            : Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_DISABLED;

        return $statusLabels[$status];
    }

    /**
     * Is account email displaying allowed by current status
     *
     * @return boolean
     */
    public function isDisplayAccount()
    {
        return $this->getStatus() == Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE;
    }

    /**
     * Is boarding button displaying allowed by current scope
     *
     * @return boolean
     */
    public function isDisplayButton()
    {
        return $this->getForm()->getScope() !== Mage_Backend_Block_System_Config_Form::SCOPE_STORES;
    }

    /**
     * Is Permissions are active
     *
     * @return boolean
     */
    public function isPermissionActive()
    {
        return $this->getStatus() === Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE;
    }
}
