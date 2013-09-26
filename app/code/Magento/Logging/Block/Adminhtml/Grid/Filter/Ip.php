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
namespace Magento\Logging\Block\Adminhtml\Grid\Filter;

class Ip extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Text
{
    /**
     * Core resource helper
     *
     * @var \Magento\Core\Model\Resource\Helper\Mysql4
     */
    protected $_resourceHelper;

    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Logging\Model\Resource\Helper\Mysql4 $resourceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Logging\Model\Resource\Helper\Mysql4 $resourceHelper,
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

        $fieldExpression = new \Zend_Db_Expr('INET_NTOA(#?)');
        $likeExpression = $this->_resourceHelper->addLikeEscape($value, array('position' => 'any'));
        return array('field_expr' => $fieldExpression, 'like' => $likeExpression);
    }
}
