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
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Usecustom extends \Magento\Framework\App\Config\Value
{
    /**
     * Writer of configuration storage
     *
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_configWriter;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configWriter = $configWriter;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Validate custom url
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == 1) {
            $customUrl = $this->getData('groups/url/fields/custom/value');
            if (empty($customUrl)) {
                throw new \Magento\Framework\Model\Exception(__('Please specify the admin custom URL.'));
            }
        }

        return $this;
    }

    /**
     * Delete custom admin url from configuration if "Use Custom Admin Url" option disabled
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $value = $this->getValue();

        if (!$value) {
            $this->_configWriter->delete(
                Custom::XML_PATH_SECURE_BASE_URL,
                Custom::CONFIG_SCOPE,
                Custom::CONFIG_SCOPE_ID
            );
            $this->_configWriter->delete(
                Custom::XML_PATH_UNSECURE_BASE_URL,
                Custom::CONFIG_SCOPE,
                Custom::CONFIG_SCOPE_ID
            );
        }

        return $this;
    }
}
