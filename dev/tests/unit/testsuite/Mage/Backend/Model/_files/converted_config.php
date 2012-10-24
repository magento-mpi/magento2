<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_Backend
     * @subpackage  unit_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */
    ?>
<?php return array(
    'config' => array(
        'system' => array(
            'tabs' => array(
                'tab_1' => array(
                    'id' => 'tab_1',
                    'label' => 'Tab 1 New'
                )
            ),
            'sections' => array(
                'section_1' => array(
                    'id' => 'section_1',
                    'label' => 'Section 1 New',
                    'groups' => array(
                        'group_1' => array(
                            'id' => 'group_1',
                            'label' => 'Group 1 New',
                            'fields' => array(
                                'field_2' => array(
                                    'id' => 'field_2',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'label' => 'Field 2',
                                    'type' => 'text',
                                    'backend_model' => 'Mage_Backend_Model_Config_Backend_Encrypted'
                                )
                            ),
                            'type' => 'text'
                        ),
                        'group_2' => array(
                            'id' => 'group_2',
                            'label' => 'Group 2',
                            'fields' => array(
                                'field_3' => array(
                                    'id' => 'field_3',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'label' => 'Field 3',
                                    'type' => 'text'
                                )
                            ),
                            'type' => 'text'
                        )
                    ),
                    'type' => 'text',
                    'tab' => 'tab_1'
                ),
                'section_2' => array(
                    'id' => 'section_2',
                    'label' => 'Section 2',
                    'type' => 'text',
                    'tab' => 'tab_2',
                    'groups' => array(
                        'group_3' => array(
                            'id' => 'group_3',
                            'label' => 'Group 3',
                            'fields' => array(
                                'field_3' => array(
                                    'id' => 'field_3',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'label' => 'Field 3',
                                    'type' => 'text'
                                ),
                                'field_4' => array(
                                    'id' => 'field_4',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'label' => 'Field 4',
                                    'type' => 'text',
                                    'backend_model' => 'Mage_Backend_Model_Config_Backend_Encrypted'
                                )
                            ),
                            'type' => 'text'
                        )
                    )
                )
            )
        )
    )
);
