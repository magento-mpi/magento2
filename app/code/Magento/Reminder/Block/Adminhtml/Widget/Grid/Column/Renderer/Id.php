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
namespace Magento\Reminder\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Id
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Url Builder
     *
     * @var \Magento\Backend\Model\Url
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Url $url,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_urlBuilder = $url;
    }

    /**
     * Render customer id linked to its account edit page
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $customerId = $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        return '<a href="' . $this->_urlBuilder->getUrl('*/customer/edit',
            array('id' => $customerId)) . '">' . $customerId . '</a>';
    }
}
