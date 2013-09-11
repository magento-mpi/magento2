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
namespace Magento\Logging\Block\Adminhtml\Grid\Filter;

class Ip extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Text
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

        $fieldExpression = new \Zend_Db_Expr('INET_NTOA(#?)');
        /** @var \Magento\Core\Model\Resource\Helper\Mysql4 $resHelper */
        $resHelper = \Mage::getResourceHelper('Magento_Core');
        $likeExpression = $resHelper->addLikeEscape($value, array('position' => 'any'));
        return array('field_expr' => $fieldExpression, 'like' => $likeExpression);
    }
}
