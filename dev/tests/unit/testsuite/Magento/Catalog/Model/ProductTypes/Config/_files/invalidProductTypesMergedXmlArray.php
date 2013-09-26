<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array
(
    'without_required_type_handle' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config></config>',
        array("Element 'config': Missing child element(s). Expected is ( type ).")),
    'type_without_required_name' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><type label="some label" modelInstance="model_name" /></config>',
        array("Element 'type': The attribute 'name' is required but missing.")),
    'type_without_required_label' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><type name="some_name" modelInstance="model_name" /></config>',
        array("Element 'type': The attribute 'label' is required but missing.")),
    'type_without_required_modelInstance' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><type label="some_label" name="some_name" /></config>',
        array("Element 'type': The attribute 'modelInstance' is required but missing.")),
    'type_pricemodel_without_required_instance_attribute' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config>'
            . '<type label="some_label" name="some_name" modelInstance="model_name"><priceModel/></type></config>',
        array("Element 'priceModel': The attribute 'instance' is required but missing.")),
    'type_indexmodel_without_required_instance_attribute' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config>'
            . '<type label="some_label" name="some_name" modelInstance="model_name"><indexerModel/></type></config>',
        array("Element 'indexerModel': The attribute 'instance' is required but missing.")),
    'type_stockindexermodel_without_required_instance_attribute' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><type label="some_label" '
            . 'name="some_name" modelInstance="model_name"><stockIndexerModel/></type></config>',
        array("Element 'stockIndexerModel': The attribute 'instance' is required but missing.")),
);