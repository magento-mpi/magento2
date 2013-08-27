<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template grid type filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_System_Email_Template_Grid_Filter_Type
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected static $_types = array(
        null                                        =>  null,
        Magento_Newsletter_Model_Template::TYPE_HTML   => 'HTML',
        Magento_Newsletter_Model_Template::TYPE_TEXT   => 'Text',
    );

    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_types as $code => $label) {
            $result[] = array('value' => $code, 'label' => __($label));
        }

        return $result;
    }


    public function getCondition()
    {
        if(is_null($this->getValue())) {
            return null;
        }

        return array('eq' => $this->getValue());
    }
}
