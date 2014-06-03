<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for form element that can contain other elements
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

interface ContainerInterface
{
    /**
     * Add fields to composite composite element
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param boolean $after
     * @param boolean $isAdvanced
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function addField($elementId, $type, $config, $after = false, $isAdvanced = false);
}
