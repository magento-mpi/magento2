<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme_Label class used for system configuration
 */
namespace Magento\View\Design\Theme;

class Label
{
    /**
     * Labels collection array
     *
     * @var array
     */
    protected $_labelsCollection;

    /**
     * Constructor
     *
     * @param \Magento\View\Design\Theme\Label\ListInterface $labelList
     */
    public function __construct(\Magento\View\Design\Theme\Label\ListInterface $labelList)
    {
        $this->_labelsCollection = $labelList;
    }

    /**
     * Return labels collection array
     *
     * @param bool|string $label add empty values to result with specific label
     * @return array
     */
    public function getLabelsCollection($label = false)
    {
        $options = $this->_labelsCollection->getLabels();
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
