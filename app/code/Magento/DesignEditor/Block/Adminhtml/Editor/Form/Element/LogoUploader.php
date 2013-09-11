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
 * Form element renderer to display logo uploader element for VDE
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

class LogoUploader
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\ImageUploader
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'logo-uploader';

    /**
     * Ability to upload multiple files by default is disabled for logo
     */
    protected $_multipleFiles = false;
}
