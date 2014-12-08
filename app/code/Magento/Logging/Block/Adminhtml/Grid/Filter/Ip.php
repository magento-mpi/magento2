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
     * Construct
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Logging\Model\Resource\Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Logging\Model\Resource\Helper $resourceHelper,
        array $data = []
    ) {
        parent::__construct($context, $resourceHelper, $data);
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
        $likeExpression = $this->_resourceHelper->addLikeEscape($value, ['position' => 'any']);
        return ['field_expr' => $fieldExpression, 'like' => $likeExpression];
    }
}
