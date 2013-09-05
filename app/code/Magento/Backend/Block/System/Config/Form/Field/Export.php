<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export CSV button for shipping table rates
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_System_Config_Form_Field_Export extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        if (isset($attributes['helperFactory'])) {
            $this->_helperFactory = $attributes['helperFactory'];
            unset($attributes['helperFactory']);
        } else {
            $this->_helperFactory = Mage::getSingleton('Magento_Core_Model_Factory_Helper');
        }

        parent::__construct($attributes);
    }

    public function getElementHtml()
    {
        /** @var Magento_Backend_Block_Widget_Button $buttonBlock  */
        $buttonBlock = $this->getForm()
            ->getParent()
            ->getLayout()
            ->createBlock('Magento_Backend_Block_Widget_Button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = $this->_helperFactory->get('Magento_Backend_Helper_Data')->getUrl("*/*/exportTablerates", $params);
        $data = array(
            'label'     =>  __('Export CSV'),
            'onclick'   => "setLocation('" . $url
                . "conditionName/' + $('carriers_tablerate_condition_name').value + '/tablerates.csv' )",
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
