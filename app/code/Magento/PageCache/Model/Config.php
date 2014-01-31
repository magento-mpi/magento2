<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PageCache config model
 * Used get PageCache configuration
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model;

use Magento\App\Filesystem;

class Config extends \Magento\Object
{
    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * Path to save temporary .vcl configuration
     *
     * @var string
     */
    protected $_path = 'etc';

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_directory;

    public function __construct(array $data = array())
    {
        //$this->_directory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getVclFile()
    {
        $vclContent = 'content';
        return $vclContent;
    }
}
