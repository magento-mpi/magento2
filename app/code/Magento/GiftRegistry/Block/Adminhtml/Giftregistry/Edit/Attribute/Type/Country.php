<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Attribute\Type;

class Country
    extends \Magento\Adminhtml\Block\Widget\Form
{

    protected $_template = 'edit/type/country.phtml';

    /**
     * Select element for choosing show region option
     *
     * @return string
     */
    public function getRegionShowSelectHtml()
    {
        $select = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Html\Select')
            ->setData(array(
                'id'    => '{{prefix}}_attribute_{{id}}_show_region',
                'class' => 'select global-scope'
            ))
            ->setName('attributes[{{prefix}}][{{id}}][show_region]')
            ->setOptions(\Mage::getSingleton('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray());

        return $select->getHtml();
    }
}
