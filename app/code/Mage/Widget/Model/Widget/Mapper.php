<?php
/**
 * Map the widget.xml structure to an array format that is familiar to legacy code.
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_Widget_Mapper
{

    /**
     * Map an array of xml data based on the new XML structure into an array that resembles the old XML structure.
     *
     * @param array $xml
     * @return array
     */
    public function map(array $xml)
    {
        $widgetConfig = array();

        $widgetsXml = isset($xml['widget']) ? $xml['widget'] : array();
        foreach ($widgetsXml as $widgetXml) {
            $widgetConfig[] = array($widgetXml['@']['id'] => $this->_mapWidget($widgetXml));
        }

        return $widgetConfig;
    }

    /**
     * Map a widget from new to old structure
     *
     * @param $widgetXml
     * @return array
     */
    private function _mapWidget($widgetXml)
    {
        $widget = array('@' => array());
        $widget['@']['type'] = $widgetXml['@']['class'];
        $widget['@']['module'] = $widgetXml['@']['module'];
        $widget['name'] = $widgetXml['label'];
        $widget['description'] = $widgetXml['description'];
        $widget['is_email_compatible'] = array($widgetXml['@']['is_email_compatible']);
        if (isset($widgetXml['@']['placeholder_image'])) {
            $widget['placeholder_image'] = array($widgetXml['@']['placeholder_image']);
        }
        if (isset($widgetXml['@']['translate'])) {
            $widget['@']['translate'] = $widgetXml['@']['translate'];
        }
        if (isset($widgetXml['parameter'])) {
            $widget['parameters'] = array();
            foreach ($widgetXml['parameter'] as $parameterXml) {
                $widget['parameters'][] = array($parameterXml['@']['name'] => $this->_mapParameter($parameterXml));
            }
        }
        if (isset($widgetXml['container'])) {
            $widget['supported_containers'] = array();
            foreach ($widgetXml['container'] as $containerXml) {
                $widget['supported_containers'][]
                    = array($containerXml['@']['section'] => $this->mapContainer($containerXml));
            }
        }
        return $widget;
    }

    /**
     * Map a parameter from new to old structure
     *
     * @param $parameterXml
     * @return array
     */
    private function _mapParameter($parameterXml)
    {
        $parameter = array();
        $parameter['type'] = $parameterXml['@']['type'];
        if (isset($parameterXml['@']['visible'])) {
            $parameter['visible'] = array($parameterXml['@']['visible']);
        } else {
            $parameter['visible'] = array('true');
        }
        if (isset($parameterXml['@']['required'])) {
            $parameter['required'] = array($parameterXml['@']['required']);
        }
        if (isset($parameterXml['@']['translate'])) {
            $parameter['translate'] = array($parameterXml['@']['translate']);
        }
        if (isset($parameterXml['@']['sort_order'])) {
            $parameter['sort_order'] = array($parameterXml['@']['sort_order']);
        }
        if (isset($parameterXml['label'])) {
            $parameter['label'] = $parameterXml['label'];
        }
        if (isset($parameterXml['description'])) {
            $parameter['description'] = $parameterXml['description'];
        }
        if (isset($parameterXml['depends'])) {
            $parameter['depends'] = $this->_mapDepends($parameterXml['depends']);
        }
        if (isset($parameterXml['option'])) {
            $parameter['values'] = array();
            foreach ($parameterXml['option'] as $optionXml) {
                $parameter['values'][] = array($optionXml['@']['name'] => $this->_mapOption($optionXml));
                // Convert only the first option we find marked as selected into a 'value' element.
                if (isset($optionXml['@']['selected'])
                    && $optionXml['@']['selected']
                    && !isset($parameter['value'])
                ) {
                    $parameter['value'] = array($optionXml['@']['value']);
                }
            }
        }
        return $parameter;
    }

    /**
     * Map a container from new to old structure
     *
     * @param $containerXml
     * @return array
     */
    private function mapContainer($containerXml)
    {
        $container = array();
        $container['container_name'] = array($containerXml['@']['name']);
        $container['template'] = array();
        foreach ($containerXml['template'] as $templateXml) {
            $container['template'][] = array($templateXml['@']['name'] => array($templateXml['@']['value']));
        }
        return $container;
    }

    /**
     * Map dependency from new to old structure
     *
     * @param $dependsXml
     * @return array
     */
    private function _mapDepends($dependsXml)
    {
        // $depends = {0 => {param_name => {0 => {'value' => {0 => param_value} } } }
        $depends = array();
        // Currently we only support parameter element and it must exist
        foreach ($dependsXml['parameter'] as $parameterXml) {
            $depends[] = array(
                $parameterXml['@']['name'] => array(
                    array(
                        'value' => array($parameterXml['@']['value'])
                    )
                )
            );
        }
        return $depends;
    }

    /**
     * Map an option from new to old structure
     *
     * @param $optionXml
     * @return array
     */
    private function _mapOption($optionXml)
    {
        $option = array('@' => array());
        if (isset($optionXml['@']['translate'])) {
            $option['@']['translate'] = $optionXml['@']['translate'];
        }
        $option['value'] = array($optionXml['@']['value']);
        if (isset($optionXml['label'])) {
            $option['label'] = $optionXml['label'];
        }
        return $option;
    }

}