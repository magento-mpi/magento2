<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Block\Adminhtml\Template\Grid\Filter;

/**
 * Adminhtml system template grid type filter
 *
 * @category   Magento
 * @package    Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Type extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var array
     */
    protected static $_types = array(
        null => null,
        \Magento\Email\Model\Template::TYPE_HTML => 'HTML',
        \Magento\Email\Model\Template::TYPE_TEXT => 'Text',
    );

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_types as $code => $label) {
            $result[] = array('value' => $code, 'label' => __($label));
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }

        return array('eq' => $this->getValue());
    }
}
