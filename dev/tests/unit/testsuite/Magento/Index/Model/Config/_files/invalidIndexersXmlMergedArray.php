<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'indexer_node_without_required_attributes' => array(
        '<?xml version="1.0"?><config><indexer name="name"/><indexer instance="instance" /></config>',
        array("Element 'indexer': The attribute 'instance' is required but missing.", "Element 'indexer': The attribute"
        . " 'name' is required but missing.")),
);