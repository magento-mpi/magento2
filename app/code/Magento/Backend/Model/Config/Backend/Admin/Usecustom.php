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
 * Config backend model for "Use Custom Admin URL" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Usecustom extends \Magento\Core\Model\Config\Value
{
    /**
     * Writer of configuration storage
     *
     * @var \Magento\Core\Model\Config\Storage\WriterInterface
     */
    protected $_configWriter;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Core\Model\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\Core\Model\Config\Storage\WriterInterface $configWriter,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configWriter = $configWriter;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    /**
     * Validate custom url
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Usecustom
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == 1) {
            $customUrl = $this->getData('groups/url/fields/custom/value');
            if (empty($customUrl)) {
                \Mage::throwException(
                    __('Please specify the admin custom URL.')
                );
            }
        }

        return $this;
    }

    /**
     * Delete custom admin url from configuration if "Use Custom Admin Url" option disabled
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Usecustom
     */
    protected function _afterSave()
    {
        $value = $this->getValue();

        if (!$value) {
            $this->_configWriter->delete(
                \Magento\Backend\Model\Config\Backend\Admin\Custom::XML_PATH_SECURE_BASE_URL,
                \Magento\Backend\Model\Config\Backend\Admin\Custom::CONFIG_SCOPE,
                \Magento\Backend\Model\Config\Backend\Admin\Custom::CONFIG_SCOPE_ID
            );
            $this->_configWriter->delete(
                \Magento\Backend\Model\Config\Backend\Admin\Custom::XML_PATH_UNSECURE_BASE_URL,
                \Magento\Backend\Model\Config\Backend\Admin\Custom::CONFIG_SCOPE,
                \Magento\Backend\Model\Config\Backend\Admin\Custom::CONFIG_SCOPE_ID
            );
        }

        return $this;
    }
}
