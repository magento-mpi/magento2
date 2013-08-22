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
     * @param $xml array
     * @return array
     */
    public function map(array $xml)
    {
        $widgetConfig = array();

        $widgetsXml = isset($xml['widget']) ? $xml['widget'] : array();
        foreach ($widgetsXml as $widgetXml) {
            $widgetConfig[$widgetXml['@']['id']] = $this->_mapWidget($widgetXml);
        }

        return $widgetConfig;
    }

    /**
     * Map a widget from new to old structure
     *
     * @param $widgetXml array
     * @return array
     */
    private function _mapWidget($widgetXml)
    {
        $widget = array('@' => array());
        $widget['@']['type'] = $widgetXml['@']['class'];
        $widget['@']['module'] = $widgetXml['@']['module'];
        $widget['name'] = $widgetXml['label'][0];
        $widget['description'] = $widgetXml['description'][0];
        if (isset($widgetXml['@']['is_email_compatible'])) {
            $widget['is_email_compatible'] = $widgetXml['@']['is_email_compatible'];
        }
        if (isset($widgetXml['@']['placeholder_image'])) {
            $widget['placeholder_image'] = $widgetXml['@']['placeholder_image'];
        }
        if (isset($widgetXml['@']['translate'])) {
            $widget['@']['translate'] = str_replace('label', 'name', $widgetXml['@']['translate']);
        }
        if (isset($widgetXml['parameter'])) {
            $widget['parameters'] = array();
            foreach ($widgetXml['parameter'] as $parameterXml) {
                $widget['parameters'][$parameterXml['@']['name']] = $this->_mapParameter($parameterXml);
            }
        }
        if (isset($widgetXml['container'])) {
            $widget['supported_containers'] = array();
            foreach ($widgetXml['container'] as $containerXml) {
                $widget['supported_containers'][$containerXml['@']['section']] = $this->_mapContainer($containerXml);
            }
        }
        return $widget;
    }

    /**
     * Map a parameter from new to old structure
     *
     * @param $parameterXml array
     * @return array
     */
    private function _mapParameter($parameterXml)
    {
        $parameter = array();
        $parameter = $this->_insertTypeSpecialCases($parameterXml, $parameter);
        $parameter = $this->_insertCommonParameterData($parameterXml, $parameter);
        return $parameter;
    }

    /**
     * Map a container from new to old structure
     *
     * @param $containerXml array
     * @return array
     */
    private function _mapContainer($containerXml)
    {
        $container = array();
        $container['container_name'] = $containerXml['@']['name'];
        $container['template'] = array();
        foreach ($containerXml['template'] as $templateXml) {
            $container['template'][$templateXml['@']['name']] = $templateXml['@']['value'];
        }
        return $container;
    }

    /**
     * Map dependency from new to old structure
     *
     * @param $dependsXml array
     * @return array
     */
    private function _mapDepends($dependsXml)
    {
        // $depends = {param_name => {'value' => param_value} }
        $depends = array();
        // Currently we only support parameter element and it must exist
        foreach ($dependsXml['parameter'] as $parameterXml) {
            $depends[$parameterXml['@']['name']] = array('value' => $parameterXml['@']['value']);
        }
        return $depends;
    }

    /**
     * Map an option from new to old structure
     *
     * @param $optionXml array
     * @return array
     */
    private function _mapOption($optionXml)
    {
        $option = array('@' => array());
        if (isset($optionXml['@']['translate'])) {
            $option['@']['translate'] = $optionXml['@']['translate'];
        }
        $option['value'] = $optionXml['@']['value'];
        if (isset($optionXml['label'])) {
            $option['label'] = $optionXml['label'][0];
        }
        return $option;
    }

    /**
     * Map a renderer from new XML to a helper_block of old xml
     *
     * @param $rendererXml array
     * @return array
     */
    private function _mapRenderer($rendererXml)
    {
        $helperBlock = array();

        $helperBlock['type'] = $rendererXml['@']['class'];
        $helperBlock['data'] = $this->_compressElements($rendererXml['data']);

        return $helperBlock;
    }

    /**
     * Takes an array of elements and compresses it to remove all intermediate arrays.
     *
     * @param $elements array
     * @return array
     */
    private function _compressElements($elements)
    {
        // $elements = {0 => {button => {0 => {open => {0 => 'text'}, @ => {translate => open}
        $data = $elements[0];
        if (!is_array($data)) {
            return $data;
        }

        $result = array();
        foreach ($data as $key => $value) {
            $result[$key] = ($key == '@') ? $value : $this->_compressElements($value);
        }
        return $result;
    }

    /**
     * Maps multiple options into a values xml container and inserts them into $parameter.
     *
     * @param $parameterXml array
     * @param $parameter array
     * @return array
     */
    private function _insertMappedOptions($parameterXml, $parameter)
    {
        if (isset($parameterXml['option'])) {
            $parameter['values'] = array();
            foreach ($parameterXml['option'] as $optionXml) {
                $parameter['values'][$optionXml['@']['name']] = $this->_mapOption($optionXml);
                // Convert only the first option we find marked as selected into a 'value' element.
                if (isset($optionXml['@']['selected'])
                    && $optionXml['@']['selected']
                    && !isset($parameter['value'])
                ) {
                    $parameter['value'] = $optionXml['@']['value'];
                }
            }
            return $parameter;
        }
        return $parameter;
    }

    /**
     * Maps xsi:type to proper type and handles special types like 'value_renderer', then injects everything
     * to $parameter.
     *
     * @param $parameterXml array
     * @param $parameter array
     * @return array
     */
    private function _insertTypeSpecialCases($parameterXml, $parameter)
    {
        $xsiType = $parameterXml['@']['type'];
        if ($xsiType == 'value_renderer') {
            $parameter['type'] = 'label';
            if (!isset($parameter['@'])) {
                $parameter['@'] = array();
            }
            $parameter['@']['type'] = 'complex';
            $parameter['helper_block'] = $this->_mapRenderer($parameterXml['renderer'][0]);
        } else if ($xsiType == 'select' || $xsiType == 'multiselect') {
            if (isset($parameterXml['@']['source_model'])) {
                $parameter['source_model'] = $parameterXml['@']['source_model'];
            }
            $parameter['type'] = $xsiType;
            $parameter = $this->_insertMappedOptions($parameterXml, $parameter);
        } else {
            $parameter['type'] = $xsiType;
        }

        return $parameter;
    }

    /**
     * Inserts common parameter value mappings into $parameter.
     *
     * @param $parameterXml array
     * @param $parameter array
     * @return array
     */
    private function _insertCommonParameterData($parameterXml, $parameter)
    {
        if (isset($parameterXml['@']['visible'])) {
            $parameter['visible'] = $parameterXml['@']['visible'];
        } else {
            $parameter['visible'] = 'true';
        }
        if (isset($parameterXml['@']['required'])) {
            $parameter['required'] = $parameterXml['@']['required'];
        }
        if (isset($parameterXml['@']['translate'])) {
            if (!isset($parameter['@'])) {
                $parameter['@'] = array();
            }
            $parameter['@']['translate'] = $parameterXml['@']['translate'];
        }
        if (isset($parameterXml['@']['sort_order'])) {
            $parameter['sort_order'] = $parameterXml['@']['sort_order'];
        }
        if (isset($parameterXml['label'])) {
            $parameter['label'] = $parameterXml['label'][0];
        }
        if (isset($parameterXml['description'])) {
            $parameter['description'] = $parameterXml['description'][0];
        }
        if (isset($parameterXml['depends'])) {
            $parameter['depends'] = $this->_mapDepends($parameterXml['depends'][0]);
        }
        return $parameter;
    }

}