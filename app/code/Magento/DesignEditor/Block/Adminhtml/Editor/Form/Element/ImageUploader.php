<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

/**
 * Form element renderer to display logo uploader element for VDE
 */
class ImageUploader
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Uploader
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'image-uploader';

    /**
     * @var string Default MIME types to accept
     */
    protected $_acceptTypesDefault = 'image/*';

    /**
     * Constructor helper
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setAccept($this->_acceptTypesDefault);
        $this->addClass('element-' . self::CONTROL_TYPE);
    }
}
