<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class ImageTest extends FileTest
{
    /**
     * Create an instance of the class that is being tested
     *
     * @param $isAjax
     * @param $value
     * @return Image
     */
    protected function getClass($value, $isAjax)
    {
        $imageForm = $this->getMock('Magento\Customer\Model\Metadata\Form\Image',
            ['_isUploadedFile'], [
                $this->localeMock,
                $this->loggerMock,
                $this->attributeMetadataMock,
                $value,
                0,
                $isAjax,
                $this->coreDataMock,
                $this->fileValidatorMock,
                $this->fileSystemMock
            ]
        );
        return $imageForm;
    }

    public function validateValueToUploadDataProvider()
    {
        $imagePath = __DIR__ . '/_files/logo.gif';
        return [
            [
                ['"realFileName" is not a valid file.'],
                ['tmp_name' => 'tmp_file', 'name' => 'realFileName'],
                ['valid' => false],
            ],
            [
                true,
                ['tmp_name' => $imagePath, 'name' => 'logo.gif'],
            ],
        ];
    }
}
