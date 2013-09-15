<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry custom attribute config model
 */
namespace Magento\GiftRegistry\Model\Attribute;

class Config extends \Magento\Core\Model\AbstractModel
{
    protected $_config = null;
    protected $_staticTypes = null;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Core\Model\Config\StorageInterface
     */
    protected $_configReader;

    /**
     * Pathes to attribute groups and types nodes
     */
    const XML_ATTRIBUTE_GROUPS_PATH = 'prototype/attribute_groups';
    const XML_ATTRIBUTE_TYPES_PATH = 'prototype/attribute_types';

    /**
     * @param \Magento\Core\Model\Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Core\Model\Config\Modules\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\Core\Model\Config\Modules\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configCacheType = $configCacheType;
        $this->_configReader = $configReader;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Load config from giftregistry.xml files and try to cache it
     *
     * @return \Magento\Simplexml\Config
     */
    public function getXmlConfig()
    {
        if (is_null($this->_config)) {
            $cachedXml = $this->_configCacheType->load('giftregistry_config');
            if ($cachedXml) {
                $xmlConfig = new \Magento\Simplexml\Config($cachedXml);
            } else {
                $xmlConfig = new \Magento\Simplexml\Config();
                $xmlConfig->loadString('<?xml version="1.0"?><prototype></prototype>');
                $this->_configReader->loadModulesConfiguration('giftregistry.xml', $xmlConfig);
                $this->_configCacheType->save($xmlConfig->getXmlString(), 'giftregistry_config');
            }
            $this->_config = $xmlConfig;
        }
        return $this->_config;
    }

    /**
     * Return array of default options
     *
     * @return array
     */
    protected function _getDefaultOption()
    {
        return array(array(
            'value' => '',
            'label' => __('-- Please select --'))
        );
    }

    /**
     * Return array of attribute types for using as options
     *
     * @return array
     */
    public function getAttributeTypesOptions()
    {
        $options = array_merge($this->_getDefaultOption(), array(
            array(
                'label' => __('Custom Types'),
                'value' => $this->getAttributeCustomTypesOptions()
            ),
            array(
                'label' => __('Static Types'),
                'value' => $this->getAttributeStaticTypesOptions()
            )
        ));
        return $options;
    }

    /**
     * Return array of attribute groups for using as options
     *
     * @return array
     */
    public function getAttributeGroupsOptions()
    {
        $options = $this->_getDefaultOption();
        $groups = $this->getAttributeGroups();

        if (is_array($groups)) {
            foreach ($groups as $code => $group) {
                if ($group['visible']) {
                    $options[] = array(
                        'value' => $code,
                        'label' => $group['label']
                    );
                }
            }
        }
        return $options;
    }

    /**
     * Return array of attribute groups
     *
     * @return array
     */
    public function getAttributeGroups()
    {
        $groups = $this->getXmlConfig()->getNode(self::XML_ATTRIBUTE_GROUPS_PATH);
        if ($groups) {
            return $groups->asCanonicalArray();
        }
    }

    /**
     * Return array of static attribute types for using as options
     *
     * @return array
     */
    public function getStaticTypes()
    {
        if (is_null($this->_staticTypes)) {
            $staticTypes = array();
            foreach (array('registry', 'registrant') as $node) {
                $node = $this->getXmlConfig()->getNode('prototype/' . $node . '/attributes/static');
                if ($node) {
                    $staticTypes = array_merge($staticTypes, $node->asCanonicalArray());
                }
            }
            $this->_staticTypes = $staticTypes;
        }
        return $this->_staticTypes;
    }

    /**
     * Return array of codes of static attribute types
     *
     * @return array
     */
    public function getStaticTypesCodes()
    {
        return array_keys($this->getStaticTypes());
    }

    /**
     * Check if attribute is in registrant group
     *
     * @param string $attribute
     * @return bool
     */
    public function isRegistrantAttribute($attribute)
    {
        foreach ($this->getStaticTypes() as $code => $data) {
            if ($attribute == $code && $data['group'] == 'registrant') {
                return true;
            }
        }
        return false;
    }

    /**
     * Return code of static date attribute type
     *
     * @return null|string
     */
    public function getStaticDateType()
    {
        foreach ($this->getStaticTypes() as $code => $type) {
            if (isset($type['type']) && $type['type'] == 'date') {
                return $code;
            }
        }
        return null;
    }

    /**
     * Return code of static region attribute type
     *
     * @return null|string
     */
    public function getStaticRegionType()
    {
        foreach ($this->getStaticTypes() as $code =>$type) {
            if (isset($type['type']) && $type['type'] == 'region') {
                return $code;
            }
        }
        return null;
    }

    /**
     * Return array of custom attribute types for using as options
     *
     * @return array
     */
    public function getAttributeCustomTypesOptions()
    {
        $types = $this->getXmlConfig()->getNode(self::XML_ATTRIBUTE_TYPES_PATH);
        $options = array();

        foreach ($types->asCanonicalArray() as $code => $type) {
            $options[] = array(
                'value' => $code,
                'label' => $type['label']
            );
        }
        return $options;
    }

    /**
     * Return array of static attribute types for using as options
     *
     * @return array
     */
    public function getAttributeStaticTypesOptions()
    {
        $options = array();
        foreach ($this->getStaticTypes() as $code => $type) {
            if (empty($type['visible'])) {
                continue;
            }
            $valueParts = array($type['type'], $code);
            if (!empty($type['group'])) {
                $valueParts[] = $type['group'];
            }

            $options[] = array(
                'value' => implode(':', $valueParts),
                'label' => $type['label']
            );
        }
        return $options;
    }
}
