<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array
(
    'entity_without_required_name' => array(
        '<?xml version="1.0"?><config><entity  label="test" model="test" behaviorModel="test" /></config>',
        array("Element 'entity': The attribute 'name' is required but missing.")),
    'entity_without_required_label' => array(
        '<?xml version="1.0"?><config><entity name="test_name" model="test" behaviorModel="test" /></config>',
        array("Element 'entity': The attribute 'label' is required but missing.")),
    'entity_without_required_behaviormodel' => array(
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" model="test" /></config>',
        array("Element 'entity': The attribute 'behaviorModel' is required but missing.")),
    'entity_without_required_model' => array(
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" behaviorModel="test" /></config>',
        array("Element 'entity': The attribute 'model' is required but missing.")),
    'entity_with_notallowed_atrribute' => array(
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" '
            . 'model="test" behaviorModel="test" notallowed="text" /></config>',
        array("Element 'entity', attribute 'notallowed': The attribute 'notallowed' is not allowed.")),
    'entity_model_with_invalid_value' => array(
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" model="afwer34" '
            . 'behaviorModel="test" /></config>',
        array("Element 'entity', attribute 'model': [facet 'pattern'] The value 'afwer34' is not "
            . "accepted by the pattern '[A-Za-z_\\\\\\\\]+'.",
        "Element 'entity', attribute 'model': 'afwer34' is not a valid value of the atomic type 'modelName'.")),
    'entity_behaviorModel_with_invalid_value' => array(
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" model="test" behaviorModel="666" />'
        . '</config>',
        array("Element 'entity', attribute 'behaviorModel': [facet 'pattern'] The value '666' is not accepted by "
            . "the pattern '[A-Za-z_\\\\\\\\]+'.",
        "Element 'entity', attribute 'behaviorModel': '666' is not a valid value of the atomic type 'modelName'.")),
);
