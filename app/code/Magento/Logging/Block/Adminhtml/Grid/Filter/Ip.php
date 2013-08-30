<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ip-address grid filter
 */
class Magento_Logging_Block_Adminhtml_Grid_Filter_Ip extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Text
{
    /**
     * Collection condition filter getter
     *
     * @return array
     */
    public function getCondition()
    {
        $value = $this->getValue();
        if (preg_match('/^(\d+\.){3}\d+$/', $value)) {
            return ip2long($value);
        }

        $fieldExpression = new Zend_Db_Expr('INET_NTOA(#?)');
        /** @var Magento_Core_Model_Resource_Helper_Mysql4 $resHelper */
        $resHelper = Mage::getResourceHelper('Magento_Core');
        $likeExpression = $resHelper->addLikeEscape($value, array('position' => 'any'));
        return array('field_expr' => $fieldExpression, 'like' => $likeExpression);
    }
}
