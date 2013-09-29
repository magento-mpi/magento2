<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display file element for VDE
 *
 * @method \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Uploader setAccept($accept)
 * @method \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Uploader setMultiple(bool $isMultiple)
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

class Uploader extends \Magento\Data\Form\Element\File
{
    //const CONTROL_TYPE = 'uploader';

    /**
     * Additional html attributes
     *
     * @var array
     */
    protected $_htmlAttributes = array('accept', 'multiple');

    /**
     * Html attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
