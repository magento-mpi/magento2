<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display file element
 */
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element;

class File extends \Magento\Framework\Data\Form\Element\File
{
    /**
     * Additional html attributes
     *
     * @var string[]
     */
    protected $_htmlAttributes = array('accept', 'multiple');

    /**
     * Html attributes
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
