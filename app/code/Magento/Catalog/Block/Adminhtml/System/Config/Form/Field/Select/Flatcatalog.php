<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System configuration shipping methods allow all countries selec
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\System\Config\Form\Field\Select;

class Flatcatalog
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if (!$this->helper('Magento\Catalog\Helper\Category\Flat')->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
