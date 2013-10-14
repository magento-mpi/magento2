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
 * Database saving file helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper\File;

class MediaHelper extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * Constructor
     *
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Date $date
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Date $date
    ) {
        parent::__construct($context);
        $this->_date = $date;
    }

    /**

     * )
     *
     * @param  string $path
     * @throws \Magento\Core\Exception
     * @return array
     */
    /**
     * Collect file info
     *
     * Return array(
     *  filename    => string
     *  content     => string|bool
     *  update_time => string
     *  directory   => string
     *
     * @param string $mediaDirectory
     * @param string $path
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function collectFileInfo($mediaDirectory, $path)
    {
        $path = ltrim($path, '\\/');
        $fullPath = $mediaDirectory . DS . $path;

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            throw new \Magento\Core\Exception(__('File %1 does not exist', $fullPath));
        }
        if (!is_readable($fullPath)) {
            throw new \Magento\Core\Exception(__('File %1 is not readable', $fullPath));
        }

        $path = str_replace(array('/', '\\'), '/', $path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = null;
        }

        return array(
            'filename'      => basename($path),
            'content'       => @file_get_contents($fullPath),
            'update_time'   => $this->_date->date(),
            'directory'     => $directory
        );
    }
}
