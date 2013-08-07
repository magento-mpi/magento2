<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export CSV button for shipping table rates
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Form_Field_Export extends Magento_Data_Form_Element_Abstract
{
    /**
     * @var Mage_Core_Model_Factory_Helper
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
            $this->_helperFactory = Mage::getSingleton('Mage_Core_Model_Factory_Helper');
        }

        parent::__construct($attributes);
    }

    public function getElementHtml()
    {
        /** @var Mage_Backend_Block_Widget_Button $buttonBlock  */
        $buttonBlock = $this->getForm()
            ->getParent()
            ->getLayout()
            ->createBlock('Mage_Backend_Block_Widget_Button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = $this->_helperFactory->get('Mage_Backend_Helper_Data')->getUrl("*/*/exportTablerates", $params);
        $data = array(
            'label'     =>  $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('Export CSV'),
            'onclick'   => "setLocation('" . $url
                . "conditionName/' + $('carriers_tablerate_condition_name').value + '/tablerates.csv' )",
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
