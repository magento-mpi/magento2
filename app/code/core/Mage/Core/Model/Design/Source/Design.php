<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_Design_Source_Design extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Add version incompatibility note to incompatible themes
     *
     * @param string $text
     * @param string $package
     * @param string $theme
     * @return string
     */
    protected function _prepareLabel($text, $package, $theme)
    {
        $magentoVersion = Mage::getVersion();
        $isCompatible = Mage::getDesign()->isThemeCompatible('frontend', $package, $theme, $magentoVersion);
        $text = $isCompatible ? $text : Mage::helper('Mage_Core_Helper_Data')->__('%s (incompatible version)', $text);
        return $text;
    }

    /**
     * Retrieve All Design Theme Options
     *
     * @param bool $withEmpty add empty (please select) values to result
     * @todo change hardcoded value 'frontend' to constant when it is created
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $designEntitiesStructure = Mage::getDesign()->getDesignEntitiesStructure('frontend');
        $config = Mage::getDesign()->getThemeConfig('frontend');

        $this->_options = array();
        foreach ($designEntitiesStructure as $packageCode => $themes) {
            foreach ($themes as $themeCode => $skins) {
                $optGroup = array(
                    'label' => $config->getPackageTitle($packageCode)
                        . ' / ' . $config->getThemeTitle($packageCode, $themeCode),
                    'value' => array()
                );
                foreach ($skins as $skinName => $value) {
                    $label = $this->_prepareLabel($skinName, $packageCode, $themeCode);
                    $optGroup['value'][] = array(
                        'label' => $label,
                        'value' => $packageCode . '/' . $themeCode . '/' . $skinName,
                    );
                }
                $this->_options[] = $optGroup;
            }
        }
        $this->_sortByKey($this->_options, 'label'); // order by package title

        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value'=>'',
                'label'=>Mage::helper('Mage_Core_Helper_Data')->__('-- Please Select --'))
            );
        }
        return $options;
    }

    /**
     * Get all, except empty, options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getAllOptions(false);
    }

    /**
     * Get package/theme options as optgroup array
     *
     * @return array
     */
    public function getThemeOptions()
    {
        $options = array();
        $config = Mage::getDesign()->getThemeConfig('frontend');
        foreach (Mage::getDesign()->getDesignEntitiesStructure('frontend') as $package => $themes) {
            $optGroup = array('label' => $config->getPackageTitle($package), 'value' => array());
            foreach (array_keys($themes) as $theme) {
                $label = $this->_prepareLabel($config->getThemeTitle($package, $theme), $package, $theme);
                $optGroup['value'][] = array('label' => $label, 'value' => "{$package}/{$theme}");
            }
            $this->_sortByKey($optGroup['value'], 'label'); // order by theme title
            $options[] = $optGroup;
        }
        $this->_sortByKey($options, 'label'); // order by package title
        return $options;
    }

    /**
     * Sort a two-dimensional array by values by specified keys
     *
     * @param array &$options
     * @param string $key
     */
    protected function _sortByKey(&$options, $key)
    {
        usort($options, function ($a, $b) use ($key) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] < $b[$key]) ? -1 : 1;
        });
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);

        return $value;
    }
}
