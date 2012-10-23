<?php

class Tools_Migration_System_Configuration_Parser
{
    public function parse(DOMDocument $dom)
    {
        return array(
            'comment' => 'licence text',
            'tabs' => array(
                'catalog' => array(
                    '@attributes' => array(
                        'translate' => 'label',
                        'module' => 'Mage_Catalog',
                    ),
                    'label' => 'test',
                    'sort_order' => 'test',
                )
            ),
            'sections' => array(
                'catalog' => array(
                    '@attributes' => array(
                        'translate' => 'label',
                        'module' => 'Mage_Catalog',
                    ),
                    'label' => 'test',
                    'sort_order' => 'test',
                    'groups' => array(

                    )
                )
            ),

        );
    }
}
