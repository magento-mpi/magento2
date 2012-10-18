<?php
/**
 * Data fixture for Webapi config tests.
 *
 * @copyright {}
 */
return array(
    'customer' => array(
        'controller' => 'Mage_Customer_Webapi_CustomerController',
        'module' => 'Mage_Customer',
        'versions' => array(
            'v1' => array(
                'methods' => array(
                    'create' => array(
                        'documentation' => 'Create customer.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'data' => array(
                                        'type' => 'Mage_Customer_Webapi_Customer_DataStructure',
                                        'required' => true,
                                        'documentation' => 'Customer create data.',
                                    ),
                                    'optional' => array(
                                        'type' => 'string',
                                        'required' => false,
                                        'documentation' => 'may be not passed.',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'int',
                                    'documentation' => 'ID of created customer',
                                ),
                            ),
                        ),
                    ),
                    'list' => array(
                        'documentation' => 'Get customers list.',
                        'interface' => array(
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                    'update' => array(
                        'documentation' => 'Update customer.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'int',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                    'data' => array(
                                        'type' => 'array',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'multiUpdate' => array(
                        'documentation' => 'Multi update of customers.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'data' => array(
                                        'type' => 'array',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'get' => array(
                        'documentation' => 'Retrieve information about customer. Add last logged in datetime.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                    'delete' => array(
                        'documentation' => 'Delete customer.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'v2' => array(
                'methods' => array(
                    'get' => array(
                        'documentation' => 'Method for versioning testing purposes.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                    'multiDelete' => array( // This method must not be available in first version
                        'documentation' => 'Method for versioning testing purposes.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'catalogProduct' => array(
        'controller' => 'Mage_Catalog_Webapi_ProductController',
        'module' => 'Mage_Catalog',
        'versions' => array(
            'v1' => array(
                'methods' => array(
                    'get' => array(
                        'documentation' => 'Core product get.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'enterpriseCatalogProduct' => array(
        'controller' => 'Enterprise_Catalog_Webapi_ProductController',
        'module' => 'Enterprise_Catalog',
        'versions' => array(
            'v1' => array(
                'methods' => array(
                    'get' => array(
                        'documentation' => 'Enterprise product get.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'resourceWithoutControllerAndModule' => array(
        'versions' => array(
            'v1' => array(
                'methods' => array(
                    'get' => array(
                        'documentation' => 'Enterprise product get.',
                        'interface' => array(
                            'in' => array(
                                'parameters' => array(
                                    'id' => array(
                                        'type' => 'string',
                                        'required' => true,
                                        'documentation' => '',
                                    ),
                                ),
                            ),
                            'out' => array(
                                'result' => array(
                                    'type' => 'array',
                                    'documentation' => '',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
