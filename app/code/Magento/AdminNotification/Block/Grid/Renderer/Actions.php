<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Block\Grid\Renderer;

class Actions extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Helper\Url $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Helper\Url $urlHelper,
        array $data = array()
    ) {
        $this->_urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $readDetailsHtml = $row->getUrl() ? '<a class="action-details" target="_blank" href="' . $row->getUrl() . '">' . __(
            'Read Details'
        ) . '</a>' : '';

        $markAsReadHtml = !$row->getIsRead() ? '<a class="action-mark" href="' . $this->getUrl(
            '*/*/markAsRead/',
            array('_current' => true, 'id' => $row->getId())
        ) . '">' . __(
            'Mark as Read'
        ) . '</a>' : '';

        $encodedUrl = $this->_urlHelper->getEncodedUrl();
        return sprintf(
            '%s%s<a class="action-delete" href="%s" onClick="deleteConfirm(\'%s\', this.href); return false;">%s</a>',
            $readDetailsHtml,
            $markAsReadHtml,
            $this->getUrl(
                '*/*/remove/',
                array(
                    '_current' => true,
                    'id' => $row->getId(),
                    \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED => $encodedUrl
                )
            ),
            __('Are you sure?'),
            __('Remove')
        );
    }
}
