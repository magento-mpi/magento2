<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'fileFormat_node_with_required_attribute' => array(
        '<?xml version="1.0"?><config><fileFormat label="name_one" model="model"/><fileFormat name="name_one" '
        . 'model="model"/><fileFormat name="name" label="model"/></config>',
        array("Element 'fileFormat': The attribute 'name' is required but missing.", "Element 'fileFormat': The "
        . "attribute 'label' is required but missing.", "Element 'fileFormat': The attribute 'model' is required but "
        . "missing.")),
    'entity_node_with_required_attribute' => array(
        '<?xml version="1.0"?><config><entity label="name_one" model="model"/><entity name="name_one" model="model"/>'
        . '<entity label="name" name="model"/></config>',
        array("Element 'entity': The attribute 'name' is required but missing.", "Element 'entity': The attribute "
        . "'label' is required but missing.", "Element 'entity': The attribute 'model' is required but missing.")),
);
