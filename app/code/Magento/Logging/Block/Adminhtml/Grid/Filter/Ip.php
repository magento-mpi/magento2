<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ip-address grid filter
 */
class Magento_Logging_Block_Adminhtml_Grid_Filter_Ip extends Magento_Backend_Block_Widget_Grid_Column_Filter_Text
{
    /**
     * Core resource helper
     *
     * @var Magento_Core_Model_Resource_Helper
     */
    protected $_resourceHelper;

    /**
     * Construct
     *
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Logging_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Logging_Model_Resource_Helper_Mysql4 $resourceHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_resourceHelper = $resourceHelper;
    }

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
        $likeExpression = $this->_resourceHelper->addLikeEscape($value, array('position' => 'any'));
        return array('field_expr' => $fieldExpression, 'like' => $likeExpression);
    }
}
