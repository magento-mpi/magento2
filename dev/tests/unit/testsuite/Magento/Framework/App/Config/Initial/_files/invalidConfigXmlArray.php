<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'with_notallowed_handle' => [
        '<?xml version="1.0"?><config><notallowe></notallowe></config>',
        ["Element 'notallowe': This element is not expected. Expected is one of ( default, stores, websites )."],
    ]
];
