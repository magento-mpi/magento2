<?php
/**
 * Annotations fixture for Vendor_ModuleB_Model_Webapi_ModuleBData class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'stringParam' => array(
        'maxLength' => '255 chars.',
        'callInfo' => array(
            'vendorModuleBUpdate' => array('requiredInput' => 'Yes'),
            'vendorModuleBCreate' => array('requiredInput' => 'Conditionally'),
            'vendorModuleBGet' => array('returned' => 'Always'),
        ),
    ),
    'integerParam' => array(
        'default' => $typeData['parameters']['integerParam']['default'],
        'min' => 10,
        'max' => 100,
        'callInfo' => array(
            'vendorModuleBCreate' => array('requiredInput' => 'No'),
            'vendorModuleBUpdate' => array('requiredInput' => 'No'),
            'allCallsExcept' => array('calls' => 'vendorModuleBUpdate', 'requiredInput' => 'Yes'),
            'vendorModuleBGet' => array('returned' => 'Conditionally'),
        ),
    ),
    'optionalBool' => array(
        'default' => 'false',
        'summary' => 'this is summary',
        'seeLink' => array(
            'url' => 'http://google.com/',
            'title' => 'link title',
            'for' => 'link for',
        ),
        'docInstructions' => array('output' => 'noDoc'),
        'callInfo' => array(
            'vendorModuleBCreate' => array('requiredInput' => 'No'),
            'vendorModuleBUpdate' => array('requiredInput' => 'No'),
            'vendorModuleBGet' => array('returned' => 'Conditionally'),
        ),
    ),
    'complexTypeArray' => array(
        'tagStatus' => 'some status',
        'natureOfType' => 'array',
        'callInfo' => array(
            'vendorModuleBCreate' => array('requiredInput' => 'No'),
            'vendorModuleBUpdate' => array('requiredInput' => 'No'),
            'vendorModuleBGet' => array('returned' => 'Conditionally'),
        ),
    ),
);
