<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales widget search form for orders and returns block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Sales_Block_Widget_Guest_Form
    extends Magento_Core_Block_Template
    implements Magento_Widget_Block_Interface
{
    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function isEnable()
    {
        return !(Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn());
    }

    /**
     * Select element for choosing registry type
     *
     * @return array
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setData(array(
                'id'    => 'quick_search_type_id',
                'class' => 'select guest-select',
            ))
            ->setName('oar_type')
            ->setOptions($this->_getFormOptions())
            ->setExtraParams('onchange="showIdentifyBlock(this.value);"');
        return $select->getHtml();
    }

    /**
     * Get Form Options for Guest
     *
     * @return array
     */
    protected function _getFormOptions()
    {
        $options = $this->getData('identifymeby_options');
        if (is_null($options)) {
            $options = array();
            $options[] = array(
                'value' => 'email',
                'label' => 'Email Address'
            );
            $options[] = array(
                'value' => 'zip',
                'label' => 'ZIP Code'
            );
            $this->setData('identifymeby_options', $options);
        }

        return $options;
    }

    /**
     * Return quick search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('sales/guest/view');
    }
}
