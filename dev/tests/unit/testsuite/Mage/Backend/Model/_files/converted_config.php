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
                                    'showInWebsite' => 1,
                                    'label' => 'Field 2'
                                )
                            )
                        ),
                        'group_2' => array(
                            'id' => 'group_2',
                            'label' => 'Group 2',
                            'fields' => array(
                                'field_3' => array(
                                    'id' => 'field_3',
                                    'translate' => 'label',
                                    'showInWebsite' => 1,
                                    'label' => 'Field 3'
                                )
                            )
                        )
                    )
                ),
                'section_2' => array(
                    'id' => 'section_2',
                    'label' => 'Section 2'
                )
            )
        )
    )
);
