<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme_Label class used for system configuration
 */
class Mage_Core_Model_Theme_Label extends Mage_Core_Model_Abstract
{
    /**
     * Labels collection array
     *
     * @var array
     */
    protected $_labelsCollection;

    /**
     * @var Mage_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Model_Resource_Theme_CollectionFactory $collectionFactory
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Resource_Theme_CollectionFactory $collectionFactory,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Return labels collection array
     *
     * @param bool|string $label add empty values to result with specific label
     * @return array
     */
    public function getLabelsCollection($label = false)
    {
        if (!$this->_labelsCollection) {
            $themeCollection = $this->_collectionFactory->create();
            $themeCollection->setOrder('theme_title', Varien_Data_Collection::SORT_ORDER_ASC)
                ->filterVisibleThemes()
                ->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
                ->walk('checkThemeCompatible');
            $this->_labelsCollection = $themeCollection->toOptionArray();
        }
        $options = $this->_labelsCollection;
        if ($label) {
            array_unshift($options, array('value' => '', 'label' => $label));
        }
        return $options;
    }

    /**
     * Return labels collection for backend system configuration with empty value "No Theme"
     *
     * @return array
     */
    public function getLabelsCollectionForSystemConfiguration()
    {
        return $this->getLabelsCollection($this->_helper->__('-- No Theme --'));
    }
}
