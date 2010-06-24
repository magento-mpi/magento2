<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Payment
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_pages;

    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();

        $this->setForm($form);

        $model = Mage::registry('current_app');
        $data = $model->getFormData();

        $fieldset = $form->addFieldset('payment', array('legend' => $this->__('Payment Methods')));

        $fieldset->addField('conf/native/paypal/isActive', 'checkbox', array(
            'name'      => 'conf[native][paypal][isActive]',
            'label'     => 'Activate paypal for this store',
            'value'     => 1,
            'checked'   => isset($data['conf[native][paypal][isActive]']),
        ));

        $fieldset->addField('conf/native/defaultCheckout/isActive', 'checkbox', array(
            'name'      => 'conf[native][defaultCheckout][isActive]',
            'label'     => 'Use Default Checkout method',
            'value'     => 1,
            'checked'   => isset($data['conf[native][defaultCheckout][isActive]']),
        ));

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Payment Methods');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Payment Methods');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
