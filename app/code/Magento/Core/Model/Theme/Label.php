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
 * Theme_Label class used for system configuration
 */
class Magento_Core_Model_Theme_Label
{
    /**
     * Labels collection array
     *
     * @var array
     */
    protected $_labelsCollection;

    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory
     */
    public function __construct(Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
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
            $themeCollection->setOrder('theme_title', \Magento\Data\Collection::SORT_ORDER_ASC);
            $themeCollection->filterVisibleThemes()->addAreaFilter(Magento_Core_Model_App_Area::AREA_FRONTEND);
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
        return $this->getLabelsCollection(__('-- No Theme --'));
    }
}
