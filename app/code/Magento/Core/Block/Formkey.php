<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend form key content block
 */
namespace Magento\Core\Block;

class Formkey extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param Template\Context $context
     * @param \Magento\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Data\Form\FormKey $formKey,
        array $data = array()
    ) {
        $this->formKey = $formKey;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
