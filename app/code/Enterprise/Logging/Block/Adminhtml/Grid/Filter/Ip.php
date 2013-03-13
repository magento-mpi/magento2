<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ip-address grid filter
 */
class Enterprise_Logging_Block_Adminhtml_Grid_Filter_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text
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

        $resHelper = Mage::getResourceHelper('Enterprise_Logging');
        $fieldExpression = $resHelper->getInetNtoaExpr();
        $likeExpression = $resHelper->addLikeEscape($value, array('position' => 'any'));
        return array('field_expr' => $fieldExpression, 'like' => $likeExpression);
    }
}
