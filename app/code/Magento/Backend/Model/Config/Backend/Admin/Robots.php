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
 * Config backend model for robots.txt
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Robots extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_filesystem = $filesystem;
        $this->_filePath = \Mage::getBaseDir() . '/robots.txt';
    }


    /**
     * Return content of default robot.txt
     *
     * @return bool|string
     */
    protected function _getDefaultValue()
    {
        $file = $this->_filePath;
        if ($this->_filesystem->isFile($file)) {
            return $this->_filesystem->read($file);
        }
        return false;
    }

    /**
     * Load default content from robots.txt if customer does not define own
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Robots
     */
    protected function _afterLoad()
    {
        if (!(string) $this->getValue()) {
            $this->setValue($this->_getDefaultValue());
        }

        return parent::_afterLoad();
    }

    /**
     * Check and process robots file
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Robots
     */
    protected function _afterSave()
    {
        if ($this->getValue()) {
            $this->_filesystem->write($this->_filePath, $this->getValue());
        }

        return parent::_afterSave();
    }
}
