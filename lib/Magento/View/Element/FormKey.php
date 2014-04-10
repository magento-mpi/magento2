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
namespace Magento\View\Element;

class FormKey extends \Magento\View\Element\AbstractBlock
{
    /**
     * @var \Magento\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\Data\Form\FormKey $formKey,
        array $data = array()
    ) {
        $this->formKey = $formKey;
        parent::__construct($context, $data);
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

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        return '<input name="form_key" type="hidden" value="' . $this->getFormKey() . '" />';
    }
}
