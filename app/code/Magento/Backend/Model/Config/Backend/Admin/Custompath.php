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
 * Config backend model for "Custom Admin Path" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Custompath extends \Magento\Core\Model\Config\Value
{
    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_backendData = $backendData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Check whether redirect should be set
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Custom
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            $this->_backendData->clearAreaFrontName();
            $this->_coreRegistry->register('custom_admin_path_redirect', true, true);
        }
        return $this;
    }
}
