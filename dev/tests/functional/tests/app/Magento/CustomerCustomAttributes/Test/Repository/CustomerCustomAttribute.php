<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CustomerCustomAttribute
 * Data for creation Customer Custom Attribute
 */
class CustomerCustomAttribute extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['text_field'] = [
            'frontend_label' => 'TextField_Customer_%isolation%',
            'attribute_code' => 'textfield_%isolation%',
            'frontend_input' => 'Text Field',
            'sort_order' => '10',
        ];

        $this->_data['text_area'] = [
            'frontend_label' => 'TextArea_Customer_%isolation%',
            'attribute_code' => 'textarea_%isolation%',
            'frontend_input' => 'Text Area',
            'sort_order' => '20',
        ];

        $this->_data['multiple_line'] = [
            'frontend_label' => 'MultipleLine_Customer_%isolation%',
            'attribute_code' => 'multiple_%isolation%',
            'frontend_input' => 'Multiple Line',
            'scope_multiline_count' => '2',
            'sort_order' => '30',
        ];

        $this->_data['date'] = [
            'frontend_label' => 'Date_Customer_%isolation%',
            'attribute_code' => 'date_%isolation%',
            'frontend_input' => 'Date',
            'sort_order' => '40',
        ];

        $this->_data['dropdown'] = [
            'frontend_label' => 'Dropdown_Customer_%isolation%',
            'attribute_code' => 'dropdown_%isolation%',
            'frontend_input' => 'Dropdown',
            'sort_order' => '50',
            'option' => [
                'value' => [
                    'option_0' => [
                        '0' => 'option1%isolation%',
                    ],
                    'option_1' => [
                        '0' => 'option2%isolation%',
                    ],
                ],
            ],
        ];

        $this->_data['multiple_select'] = [
            'frontend_label' => 'MultipleSelect_Customer_%isolation%',
            'attribute_code' => 'multi_%isolation%',
            'frontend_input' => 'Multiple Select',
            'sort_order' => '60',
            'option' => [
                'value' => [
                    'option_0' => [
                        '0' => 'option1%isolation%',
                    ],
                    'option_1' => [
                        '0' => 'option2%isolation%',
                    ],
                ],
            ],
        ];

        $this->_data['yes_no'] = [
            'frontend_label' => 'YesNo_Customer_%isolation%',
            'attribute_code' => 'yesno_%isolation%',
            'frontend_input' => 'Yes/No',
            'sort_order' => '70',
        ];

        $this->_data['file'] = [
            'frontend_label' => 'File_Customer_%isolation%',
            'attribute_code' => 'file_%isolation%',
            'frontend_input' => 'File (attachment)',
            'sort_order' => '80',
        ];

        $this->_data['image'] = [
            'frontend_label' => 'Image_Customer_%isolation%',
            'attribute_code' => 'image_%isolation%',
            'frontend_input' => 'Image File',
            'sort_order' => '90',
        ];
    }
}
