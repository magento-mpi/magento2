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
 * Files uploader block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Content;

class Uploader
    extends \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content\Uploader
{
    /**
     * Path to uploader template
     *
     * @var string
     */
    protected $_template = 'editor/tools/files/content/uploader.phtml';
}
