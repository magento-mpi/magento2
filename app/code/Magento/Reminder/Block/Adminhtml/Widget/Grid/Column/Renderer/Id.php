<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for customer id
 */
class Magento_Reminder_Block_Adminhtml_Widget_Grid_Column_Renderer_Id
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Url Builder
     *
     * @var Magento_Backend_Model_Url
     */
    protected $_urlBuilder;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Backend_Model_Url $url
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Backend_Model_Url $url,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_urlBuilder = $url;
    }

    /**
     * Render customer id linked to its account edit page
     *
     * @param   Magento_Object $row
     * @return  string
     */
    protected function _getValue(Magento_Object $row)
    {
        $customerId = $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        return '<a href="' . $this->_urlBuilder->getUrl('*/customer/edit',
            array('id' => $customerId)) . '">' . $customerId . '</a>';
    }
}
