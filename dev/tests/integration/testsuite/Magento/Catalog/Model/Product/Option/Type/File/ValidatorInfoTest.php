<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Option\Type\File;

/**
 * @magentoDataFixture Magento/Catalog/_files/validate_image_info.php
 */
class ValidatorInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidatorInfo
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Product\Option\Type\File\ValidateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validateFactoryMock;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->validateFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Option\Type\File\ValidateFactory',
            ['create']
        );
        $this->model = $this->objectManager->create(
            'Magento\Catalog\Model\Product\Option\Type\File\ValidatorInfo',
            [
                'validateFactory' => $this->validateFactoryMock,
            ]
        );
    }

    /**
     * @return void
     */
    public function testExceptionWithErrors()
    {
        $this->setExpectedException(
            '\Magento\Framework\Model\Exception',
            "The file 'test.jpg' for 'MediaOption' has an invalid extension.\n"
            . "The file 'test.jpg' for 'MediaOption' has an invalid extension.\n"
            . "Maximum allowed image size for 'MediaOption' is 2000x2000 px.\n"
            . "The file 'test.jpg' you uploaded is larger than the 2 megabytes allowed by our server."
        );

        $validateMock = $this->getMock('Zend_Validate', ['isValid', 'getErrors']);
        $validateMock->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $validateMock->expects($this->exactly(2))->method('getErrors')->will($this->returnValue([
            \Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION,
            \Zend_Validate_File_Extension::FALSE_EXTENSION,
            \Zend_Validate_File_ImageSize::WIDTH_TOO_BIG,
            \Zend_Validate_File_FilesSize::TOO_BIG,
        ]));
        $this->validateFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($validateMock));

        $this->model->validate(
            $this->getOptionValue(),
            $this->getProductOption()
        );
    }

    /**
     * @return void
     */
    public function testExceptionWithoutErrors()
    {
        $this->setExpectedException(
            '\Magento\Framework\Model\Exception',
            "Please specify the product's required option(s)."
        );

        $validateMock = $this->getMock('Zend_Validate', ['isValid', 'getErrors']);
        $validateMock->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $validateMock->expects($this->exactly(1))->method('getErrors')->will($this->returnValue(false));
        $this->validateFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($validateMock));

        $this->model->validate(
            $this->getOptionValue(),
            $this->getProductOption()
        );
    }

    /**
     * @return void
     */
    public function testValidate()
    {
        $validateMock = $this->getMock('Zend_Validate', ['isValid']);
        $validateMock->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->validateFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($validateMock));
        $this->assertTrue(
            $this->model->validate(
                $this->getOptionValue(),
                $this->getProductOption()
            )
        );
    }

    /**
     * @param array $options
     * @return \Magento\Catalog\Model\Product\Option
     */
    protected function getProductOption(array $options = [])
    {
        $data = [
            'option_id' => '1',
            'product_id' => '4',
            'type' => 'file',
            'is_require' => '1',
            'sku' => null,
            'max_characters' => null,
            'file_extension' => null,
            'image_size_x' => '2000',
            'image_size_y' => '2000',
            'sort_order' => '0',
            'default_title' => 'MediaOption',
            'store_title' => null,
            'title' => 'MediaOption',
            'default_price' => '5.0000',
            'default_price_type' => 'fixed',
            'store_price' => null,
            'store_price_type' => null,
            'price' => '5.0000',
            'price_type' => 'fixed',
        ];
        $option = $this->objectManager->create(
            'Magento\Catalog\Model\Product\Option',
            [
                'data' => array_merge($data, $options)
            ]
        );

        return $option;
    }

    /**
     * @return array
     */
    protected function getOptionValue()
    {
        $file     = 'magento_small_image.jpg';
        $tmpPath = 'var/tmp/' . $file;

        /** @var \Magento\Framework\App\Filesystem $filesystem */
        $filesystem = $this->objectManager->get('Magento\Framework\App\Filesystem');
        $tmpDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::TMP_DIR);
        $filePath = $tmpDirectory->getAbsolutePath($file);

        return [
            'title'      => 'test.jpg',
            'quote_path' => $tmpPath,
            'order_path' => $tmpPath,
            'secret_key' => substr(md5(file_get_contents($filePath)), 0, 20),
        ];
    }
}
