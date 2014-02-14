<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar;

use Magento\View\Element\AbstractBlock;

class Add extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/attribute/set/toolbar/add.phtml';

    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Save Attribute Set'),
            'class' => 'save',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#set-prop-form'),
                ),
            ),
        ));
        $this->addChild('back_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('catalog/*/').'\')',
            'class' => 'back'
        ));

        $this->addChild('setForm', 'Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main\Formset');
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return __('Add New Attribute Set');
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('setForm');
    }

    /**
     * Return id of form, used by this block
     *
     * @return string
     */
    public function getFormId()
    {
        return $this->getChildBlock('setForm')->getForm()->getId();
    }
}
