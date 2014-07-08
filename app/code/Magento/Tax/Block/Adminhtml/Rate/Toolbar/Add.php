<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax class product toolbar
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Block\Adminhtml\Rate\Toolbar;

use Magento\Framework\View\Element\Template;

class Add extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\ContainerInterface
{
    /**
     * @var string
     */
    protected $_template = 'toolbar/rate/add.phtml';

    /**
     * @var \Magento\Backend\Block\Widget\Button\ButtonList
     */
    protected $buttonList;

    /**
     * @var \Magento\Backend\Block\Widget\Button\ToolbarInterface
     */
    protected $toolbar;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @param \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
        \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar,
        array $data = array())
    {
        $this->buttonList = $buttonList;
        $this->toolbar = $toolbar;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'add',
            array(
                'label' => __('Add New Tax Rate'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('tax/rate/add') . '\'',
                'class' => 'add primary add-tax-rate'
            )
        );

        $this->toolbar->pushButtons($this, $this->buttonList);
        return parent::_prepareLayout();
    }

    /**
     * Public wrapper for the button list
     *
     * @param string $buttonId
     * @param array $data
     * @param integer $level
     * @param integer $sortOrder
     * @param string|null $region That button should be displayed in ('toolbar', 'header', 'footer', null)
     * @return $this
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
        $this->buttonList->add($buttonId, $data, $level, $sortOrder, $region);
        return $this;
    }

    /**
     * Public wrapper for the button list
     *
     * @param string $buttonId
     * @return $this
     */
    public function removeButton($buttonId)
    {
        $this->buttonList->remove($buttonId);
        return $this;
    }

    /**
     * Public wrapper for protected _updateButton method
     *
     * @param string $buttonId
     * @param string|null $key
     * @param string $data
     * @return $this
     */
    public function updateButton($buttonId, $key, $data)
    {
        $this->buttonList->update($buttonId, $key, $data);
        return $this;
    }

    /**
     * Check whether button rendering is allowed in current context
     *
     * @param \Magento\Backend\Block\Widget\Button\Item $item
     * @return bool
     */
    public function canRender(\Magento\Backend\Block\Widget\Button\Item $item)
    {
        return !$item->isDeleted();
    }
}
