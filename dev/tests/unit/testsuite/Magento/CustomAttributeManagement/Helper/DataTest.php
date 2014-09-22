<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CustomAttributeManagement\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomAttributeManagement\Helper\Data
     */
    protected $_helper;

    /**
     * Set up
     */
    protected function setUp()
    {
        $contextMock = $this->getMockBuilder('Magento\Framework\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $filterManagerMock = $this->getMock(
            'Magento\Framework\Filter\FilterManager',
            array('stripTags'),
            array(),
            '',
            false
        );

        $filterManagerMock->expects($this->any())
            ->method('stripTags')
            ->will($this->returnValue('stripTags'));

        $this->_helper = new \Magento\CustomAttributeManagement\Helper\Data(
            $contextMock,
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Framework\Stdlib\DateTime\TimezoneInterface'),
            $filterManagerMock
        );
    }

    /**
     * @param string $frontendInput
     * @param array $validateRules
     * @param array $result
     * @dataProvider checkValidateRulesDataProvider
     */
    public function testCheckValidateRules($frontendInput, $validateRules, $result)
    {
        $this->assertEquals($result, $this->_helper->checkValidateRules($frontendInput, $validateRules));
    }

    /**
     * @return array
     */
    public function checkValidateRulesDataProvider()
    {
        return array(
            array(
                'text',
                array('min_text_length' => 1, 'max_text_length' => 2),
                array()
            ),
            array(
                'text',
                array('min_text_length' => 3, 'max_text_length' => 2),
                array(__('Please correct the values for minimum and maximum text length validation rules.'))
            ),
            array(
                'textarea',
                array('min_text_length' => 1, 'max_text_length' => 2),
                array()
            ),
            array(
                'textarea',
                array('min_text_length' => 3, 'max_text_length' => 2),
                array(__('Please correct the values for minimum and maximum text length validation rules.'))
            ),
            array(
                'multiline',
                array('min_text_length' => 1, 'max_text_length' => 2),
                array()
            ),
            array(
                'multiline',
                array('min_text_length' => 3, 'max_text_length' => 2),
                array(__('Please correct the values for minimum and maximum text length validation rules.'))
            ),
            array(
                'date',
                array('date_range_min' => '1', 'date_range_max' => '2'),
                array()
            ),
            array(
                'date',
                array('date_range_min' => '3', 'date_range_max' => '2'),
                array(__('Please correct the values for minimum and maximum date validation rules.'))
            ),
            array(
                'empty',
                array('date_range_min' => '3', 'date_range_max' => '2'),
                array()
            )
        );
    }

    public function testGetAttributeInputTypes()
    {
        $inputTypes = array(
            'text' => array(
                'label' => __('Text Field'),
                'manage_options' => false,
                'validate_types' => array('min_text_length', 'max_text_length'),
                'validate_filters' => array('alphanumeric', 'numeric', 'alpha', 'url', 'email'),
                'filter_types' => array('striptags', 'escapehtml'),
                'backend_type' => 'varchar',
                'default_value' => 'text'
            ),
            'textarea' => array(
                'label' => __('Text Area'),
                'manage_options' => false,
                'validate_types' => array('min_text_length', 'max_text_length'),
                'validate_filters' => array(),
                'filter_types' => array('striptags', 'escapehtml'),
                'backend_type' => 'text',
                'default_value' => 'textarea'
            ),
            'multiline' => array(
                'label' => __('Multiple Line'),
                'manage_options' => false,
                'validate_types' => array('min_text_length', 'max_text_length'),
                'validate_filters' => array('alphanumeric', 'numeric', 'alpha', 'url', 'email'),
                'filter_types' => array('striptags', 'escapehtml'),
                'backend_type' => 'text',
                'default_value' => 'text'
            ),
            'date' => array(
                'label' => __('Date'),
                'manage_options' => false,
                'validate_types' => array('date_range_min', 'date_range_max'),
                'validate_filters' => array('date'),
                'filter_types' => array('date'),
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
                'backend_type' => 'datetime',
                'default_value' => 'date'
            ),
            'select' => array(
                'label' => __('Dropdown'),
                'manage_options' => true,
                'option_default' => 'radio',
                'validate_types' => array(),
                'validate_filters' => array(),
                'filter_types' => array(),
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'backend_type' => 'int',
                'default_value' => false
            ),
            'multiselect' => array(
                'label' => __('Multiple Select'),
                'manage_options' => true,
                'option_default' => 'checkbox',
                'validate_types' => array(),
                'filter_types' => array(),
                'validate_filters' => array(),
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'backend_type' => 'varchar',
                'default_value' => false
            ),
            'boolean' => array(
                'label' => __('Yes/No'),
                'manage_options' => false,
                'validate_types' => array(),
                'validate_filters' => array(),
                'filter_types' => array(),
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'backend_type' => 'int',
                'default_value' => 'yesno'
            ),
            'file' => array(
                'label' => __('File (attachment)'),
                'manage_options' => false,
                'validate_types' => array('max_file_size', 'file_extensions'),
                'validate_filters' => array(),
                'filter_types' => array(),
                'backend_type' => 'varchar',
                'default_value' => false
            ),
            'image' => array(
                'label' => __('Image File'),
                'manage_options' => false,
                'validate_types' => array('max_file_size', 'max_image_width', 'max_image_heght'),
                'validate_filters' => array(),
                'filter_types' => array(),
                'backend_type' => 'varchar',
                'default_value' => false
            )
        );

        $this->assertEquals($inputTypes, $this->_helper->getAttributeInputTypes());
        foreach ($inputTypes as $key => $value) {
            $this->assertEquals($value, $this->_helper->getAttributeInputTypes($key));
        }
        $this->assertEquals(array(), $this->_helper->getAttributeInputTypes('empty'));
    }

    public function testGetFrontendInputOptions()
    {
        $result = array (
            array(
                'value' => 'text',
                'label' => __('Text Field'),
            ),
            array(
                'value' => 'textarea',
                'label' => __('Text Area'),
            ),
            array(
                'value' => 'multiline',
                'label' => __('Multiple Line'),
            ),
            array(
                'value' => 'date',
                'label' => __('Date'),
            ),
            array(
                'value' => 'select',
                'label' => __('Dropdown'),
            ),
            array(
                'value' => 'multiselect',
                'label' => __('Multiple Select'),
            ),
            array(
                'value' => 'boolean',
                'label' => __('Yes/No'),
            ),
            array(
                'value' => 'file',
                'label' => __('File (attachment)'),
            ),
            array(
                'value' => 'image',
                'label' => __('Image File'),
            ),
        );

        $this->assertEquals($result, $this->_helper->getFrontendInputOptions());
    }

    public function testGetAttributeValidateFilters()
    {
        $result = array(
            'alphanumeric' => __('Alphanumeric'),
            'numeric' => __('Numeric Only'),
            'alpha' => __('Alpha Only'),
            'url' => __('URL'),
            'email' => __('Email'),
            'date' => __('Date')
        );
        $this->assertEquals($result, $this->_helper->getAttributeValidateFilters());
    }

    public function testGetAttributeFilterTypes()
    {
        $result = array(
            'striptags' => __('Strip HTML Tags'),
            'escapehtml' => __('Escape HTML Entities'),
            'date' => __('Normalize Date')
        );
        $this->assertEquals($result, $this->_helper->getAttributeFilterTypes());
    }

    public function testGetAttributeElementScopes()
    {
        $result = array(
            'is_required' => 'website',
            'is_visible' => 'website',
            'multiline_count' => 'website',
            'default_value_text' => 'website',
            'default_value_yesno' => 'website',
            'default_value_date' => 'website',
            'default_value_textarea' => 'website',
            'date_range_min' => 'website',
            'date_range_max' => 'website'
        );
        $this->assertEquals($result, $this->_helper->getAttributeElementScopes());
    }

    /**
     * @test
     * @param string $inputType
     * @param string|false $result
     * @dataProvider getAttributeDefaultValueByInputDataProvider
     */
    public function testGetAttributeDefaultValueByInput($inputType, $result)
    {
        $this->assertEquals($result, $this->_helper->getAttributeDefaultValueByInput($inputType));
    }

    /**
     * @return array
     */
    public function getAttributeDefaultValueByInputDataProvider()
    {
        return array(
            array(
                'text',
                'scope_default_value_text',
            ),
            array(
                'textarea',
                'scope_default_value_textarea',
            ),
            array(
                'multiline',
                'scope_default_value_text',
            ),
            array(
                'date',
                'scope_default_value_date',
            ),
            array(
                'select',
                false,
            ),
            array(
                'multiselect',
                false,
            ),
            array(
                'boolean',
                'scope_default_value_yesno',
            ),
            array(
                'file',
                false,
            ),
            array(
                'image',
                false,
            ),
            array(
                'empty',
                false,
            )
        );
    }

    /**
     * @test
     * @param string $inputType
     * @param array $data
     * @param array $result
     * @dataProvider getAttributeValidateRulesDataProvider
     */
    public function testGetAttributeValidateRules($inputType, $data, $result)
    {
        $this->assertEquals($result, $this->_helper->getAttributeValidateRules($inputType, $data));
    }

    /**
     * @return array
     */
    public function getAttributeValidateRulesDataProvider()
    {
        return array(
            array(
                'text',
                array('min_text_length' => 1, 'max_text_length' => 2, 'input_validation' => 'numeric'),
                array('min_text_length' => 1, 'max_text_length' => 2, 'input_validation' => 'numeric'),
            ),
            array(
                'text',
                array('min_text_length' => 1, 'max_text_length' => 2, 'input_validation' => 'test'),
                array('min_text_length' => 1, 'max_text_length' => 2),
            ),
            array(
                'text',
                array('min_text_length' => 1),
                array('min_text_length' => 1),
            ),
            array(
                'date',
                array('date_range_max' => '01/01/2014'),
                array('date_range_max' => 1388563200),
            )
        );
    }

    public function testGetAttributeBackendModelByInputType()
    {
        $this->assertEquals(null, $this->_helper->getAttributeBackendModelByInputType('empty'));
        $this->assertEquals(
            'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
            $this->_helper->getAttributeBackendModelByInputType('date')
        );
    }

    public function testGetAttributeSourceModelByInputType()
    {
        $this->assertEquals(null, $this->_helper->getAttributeSourceModelByInputType('empty'));
        $this->assertEquals(
            'Magento\Eav\Model\Entity\Attribute\Source\Table',
            $this->_helper->getAttributeSourceModelByInputType('multiselect')
        );
    }

    public function testGetAttributeBackendTypeByInputType()
    {
        $this->assertEquals(null, $this->_helper->getAttributeBackendTypeByInputType('empty'));
        $this->assertEquals('varchar', $this->_helper->getAttributeBackendTypeByInputType('text'));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Use helper with defined EAV entity.
     */
    public function testGetUserDefinedAttributeCodes()
    {
        $this->_helper->getUserDefinedAttributeCodes();
    }

    public function testFilterPostData()
    {
        $data = array('frontend_label' => array('Label'), 'attribute_code' => 'code');;
        $result = array('frontend_label' => array('stripTags'), 'attribute_code' => 'code');
        $this->assertEquals($result, $this->_helper->filterPostData($data));
    }

    public function testFilterPostDataWithException()
    {
        $exceptionMessage = 'The attribute code is invalid.';
        $exceptionMessage .= ' Please use only letters (a-z), numbers (0-9) or underscores (_) in this field.';
        $exceptionMessage .= ' The first character should be a letter.';
        $this->setExpectedException('Magento\Framework\Model\Exception', $exceptionMessage);
        $data = array('frontend_label' => array('Label'), 'attribute_code' => 'Code');
        $this->_helper->filterPostData($data);
    }

    public function testGetAttributeFormOptions()
    {
        $this->assertEquals(
            array(array('label' => __('Default EAV Form'), 'value' => 'default')),
            $this->_helper->getAttributeFormOptions()
        );
    }
}
