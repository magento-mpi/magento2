<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter templates grid block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Newsletter\Model\Resource\Template\Collection
     */
    protected $_templateCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Newsletter\Model\Resource\Template\Collection $templateCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Newsletter\Model\Resource\Template\Collection $templateCollection,
        array $data = array()
    ) {
        $this->_templateCollection = $templateCollection;
        parent::__construct($context, $backendHelper, $data);
        $this->setEmptyText(__('No Templates Found'));
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return WidgetGrid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_templateCollection->useOnlyActual());

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'template_code',
            array(
                'header' => __('ID'),
                'index' => 'template_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
        $this->addColumn(
            'code',
            array(
                'header' => __('Template'),
                'index' => 'template_code',
                'header_css_class' => 'col-template',
                'column_css_class' => 'col-template'
            )
        );

        $this->addColumn(
            'added_at',
            array(
                'header' => __('Added'),
                'index' => 'added_at',
                'gmtoffset' => true,
                'type' => 'datetime',
                'header_css_class' => 'col-added col-date',
                'column_css_class' => 'col-added col-date'
            )
        );

        $this->addColumn(
            'modified_at',
            array(
                'header' => __('Updated'),
                'index' => 'modified_at',
                'gmtoffset' => true,
                'type' => 'datetime',
                'header_css_class' => 'col-updated col-date',
                'column_css_class' => 'col-updated col-date'
            )
        );

        $this->addColumn(
            'subject',
            array(
                'header' => __('Subject'),
                'index' => 'template_subject',
                'header_css_class' => 'col-subject',
                'column_css_class' => 'col-subject'
            )
        );

        $this->addColumn(
            'sender',
            array(
                'header' => __('Sender'),
                'index' => 'template_sender_email',
                'renderer' => 'Magento\Newsletter\Block\Adminhtml\Template\Grid\Renderer\Sender',
                'header_css_class' => 'col-sender',
                'column_css_class' => 'col-sender'
            )
        );

        $this->addColumn(
            'type',
            array(
                'header' => __('Template Type'),
                'index' => 'template_type',
                'type' => 'options',
                'options' => array(
                    \Magento\Newsletter\Model\Template::TYPE_HTML => 'html',
                    \Magento\Newsletter\Model\Template::TYPE_TEXT => 'text'
                ),
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type'
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => __('Action'),
                'index' => 'template_id',
                'sortable' => false,
                'filter' => false,
                'no_link' => true,
                'renderer' => 'Magento\Newsletter\Block\Adminhtml\Template\Grid\Renderer\Action',
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            )
        );

        return $this;
    }

    /**
     * Get row url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
