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

class Formkey extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Data\Form\FormKey $formKey,
        array $data = array()
    ) {
        $this->formKey = $formKey;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
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
