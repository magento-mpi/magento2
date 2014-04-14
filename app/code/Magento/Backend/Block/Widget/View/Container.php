<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\View;

/**
 * Magento_Backend view container block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated is not used in code
 */
class Container extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_objectId = 'id';

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Backend';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/view/container.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton(
            'back',
            array(
                'label' => __('Back'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                'class' => 'back'
            )
        );

        $this->_addButton(
            'edit',
            array(
                'label' => __('Edit'),
                'class' => 'edit',
                'onclick' => 'window.location.href=\'' . $this->getEditUrl() . '\''
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $blockName = $this->_blockGroup . '\\Block\\' . str_replace(
            ' ',
            '\\',
            ucwords(str_replace('\\', ' ', $this->_controller))
        ) . '\\View\\Plane';

        $this->setChild('plane', $this->getLayout()->createBlock($blockName));

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    /**
     * @return string
     */
    public function getViewHtml()
    {
        return $this->getChildHtml('plane');
    }
}
