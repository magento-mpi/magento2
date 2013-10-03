<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure\Element;

class Group
    extends \Magento\Backend\Model\Config\Structure\Element\AbstractComposite
{
    /**
     * Group clone model factory
     *
     * @var \Magento\Backend\Model\Config\BackendClone\Factory
     */
    protected $_cloneModelFactory;

    /**
     *
     * @var \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper
     */
    protected $_dependencyMapper;

    /**
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Backend\Model\Config\Structure\Element\Iterator\Field $childrenIterator
     * @param \Magento\Backend\Model\Config\BackendClone\Factory $cloneModelFactory
     * @param \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper $dependencyMapper
     */
    public function __construct(
        \Magento\Core\Model\App $application,
        \Magento\Backend\Model\Config\Structure\Element\Iterator\Field $childrenIterator,
        \Magento\Backend\Model\Config\BackendClone\Factory $cloneModelFactory,
        \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper $dependencyMapper
    ) {
        parent::__construct($application, $childrenIterator);
        $this->_cloneModelFactory = $cloneModelFactory;
        $this->_dependencyMapper = $dependencyMapper;
    }

    /**
     * Should group fields be cloned
     *
     * @return bool
     */
    public function shouldCloneFields()
    {
        return (isset($this->_data['clone_fields']) && !empty($this->_data['clone_fields']));
    }

    /**
     * Retrieve clone model
     *
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Magento\Core\Exception
     */
    public function getCloneModel()
    {
        if (!isset($this->_data['clone_model']) || !$this->_data['clone_model']) {
            throw new \Magento\Core\Exception('Config form fieldset clone model required to be able to clone fields');
        }
        return $this->_cloneModelFactory->create($this->_data['clone_model']);
    }

    /**
     * Populate form fieldset with group data
     *
     * @param \Magento\Data\Form\Element\Fieldset $fieldset
     */
    public function populateFieldset(\Magento\Data\Form\Element\Fieldset $fieldset)
    {
        $originalData = array();
        foreach ($this->_data as $key => $value) {
            if (!is_array($value)) {
                $originalData[$key] = $value;
            }
        }
        $fieldset->setOriginalData($originalData);
    }

    /**
     * Check whether group should be expanded
     *
     * @return bool
     */
    public function isExpanded()
    {
        return (bool) (isset($this->_data['expanded']) ? (int) $this->_data['expanded'] : false);
    }

    /**
     * Retrieve group fieldset css
     *
     * @return string
     */
    public function getFieldsetCss()
    {
        return array_key_exists('fieldset_css', $this->_data) ? $this->_data['fieldset_css'] : '';
    }

    /**
     * Retrieve field dependencies
     *
     * @param $storeCode
     * @return array
     */
    public function getDependencies($storeCode)
    {
        $dependencies = array();
        if (false == isset($this->_data['depends']['fields'])) {
            return $dependencies;
        }

        $dependencies = $this->_dependencyMapper->getDependencies($this->_data['depends']['fields'], $storeCode);
        return $dependencies;
    }
}
