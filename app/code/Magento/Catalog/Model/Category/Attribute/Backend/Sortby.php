<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category\Attribute\Backend;

/**
 * Catalog Category Attribute Default and Available Sort By Backend Model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Sortby extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Construct
     *
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(\Magento\Logger $logger, \Magento\Core\Model\Store\Config $coreStoreConfig)
    {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($logger);
    }

    /**
     * Validate process
     *
     * @param \Magento\Object $object
     * @return bool
     * @throws \Magento\Model\Exception
     */
    public function validate($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $postDataConfig = $object->getData('use_post_data_config');
        if ($postDataConfig) {
            $isUseConfig = in_array($attributeCode, $postDataConfig);
        } else {
            $isUseConfig = false;
            $postDataConfig = array();
        }

        if ($this->getAttribute()->getIsRequired()) {
            $attributeValue = $object->getData($attributeCode);
            if ($this->getAttribute()->isValueEmpty($attributeValue)) {
                if (is_array($attributeValue) && count($attributeValue) > 0) {
                } else {
                    if (!$isUseConfig) {
                        return false;
                    }
                }
            }
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                throw new \Magento\Model\Exception(__('The value of attribute "%1" must be unique.', $label));
            }
        }

        if ($attributeCode == 'default_sort_by') {
            if ($available = $object->getData('available_sort_by')) {
                if (!is_array($available)) {
                    $available = explode(',', $available);
                }
                $data = !in_array(
                    'default_sort_by',
                    $postDataConfig
                ) ? $object->getData(
                    $attributeCode
                ) : $this->_coreStoreConfig->getConfig(
                    "catalog/frontend/default_sort_by"
                );
                if (!in_array($data, $available)) {
                    throw new \Magento\Model\Exception(
                        __('Default Product Listing Sort by does not exist in Available Product Listing Sort By.')
                    );
                }
            } else {
                if (!in_array('available_sort_by', $postDataConfig)) {
                    throw new \Magento\Model\Exception(
                        __('Default Product Listing Sort by does not exist in Available Product Listing Sort By.')
                    );
                }
            }
        }

        return true;
    }

    /**
     * Before Attribute Save Process
     *
     * @param \Magento\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = array();
            }
            $object->setData($attributeCode, join(',', $data));
        }
        if (!$object->hasData($attributeCode)) {
            $object->setData($attributeCode, false);
        }
        return $this;
    }

    /**
     * After Load Attribute Process
     *
     * @param \Magento\Object $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if ($data) {
                $object->setData($attributeCode, explode(',', $data));
            }
        }
        return $this;
    }
}
