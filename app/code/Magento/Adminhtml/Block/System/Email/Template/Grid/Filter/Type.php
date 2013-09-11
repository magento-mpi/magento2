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

namespace Magento\Adminhtml\Block\System\Email\Template\Grid\Filter;

class Type
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{
    protected static $_types = array(
        null                                        =>  null,
        \Magento\Newsletter\Model\Template::TYPE_HTML   => 'HTML',
        \Magento\Newsletter\Model\Template::TYPE_TEXT   => 'Text',
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
