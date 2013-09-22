<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Hierarchy Menu source model for Layouts
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
namespace Magento\VersionsCms\Model\Source\Hierarchy\Menu;

class Layout implements Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return options for displaying Hierarchy Menu
     *
     * @param bool $withDefault Include or not default value
     * @return array
     */
    public function toOptionArray($withDefault = false)
    {
        $options = array();
        if ($withDefault) {
           $options[] = array('label' => __('Use default'), 'value' => '');
        }

        foreach (\Mage::getSingleton('Magento\VersionsCms\Model\Hierarchy\Config')->getContextMenuLayouts() as $code => $info) {
            $options[] = array(
                'label' => $info->getLabel(),
                'value' => $code
            );
        }

        return $options;
    }
}
