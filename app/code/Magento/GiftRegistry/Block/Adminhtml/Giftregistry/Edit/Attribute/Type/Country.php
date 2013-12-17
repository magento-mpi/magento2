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
    extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $sourceYesNo;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'edit/type/country.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Config\Source\Yesno $sourceYesNo
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Config\Source\Yesno $sourceYesNo,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->sourceYesNo = $sourceYesNo;
    }

    /**
     * Select element for choosing show region option
     *
     * @return string
     */
    public function getRegionShowSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setData(array(
                'id'    => '{{prefix}}_attribute_{{id}}_show_region',
                'class' => 'select global-scope'
            ))
            ->setName('attributes[{{prefix}}][{{id}}][show_region]')
            ->setOptions($this->sourceYesNo->toOptionArray());

        return $select->getHtml();
    }
}
