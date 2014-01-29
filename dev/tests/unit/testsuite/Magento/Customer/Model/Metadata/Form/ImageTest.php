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
        $fileForm = new Image(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $value,
            0,
            $isAjax,
            $this->coreDataMock,
            $this->fileValidatorMock,
            $this->fileSystemMock
        );
        return $fileForm;
    }

    public function validateValueToUploadDataProvider()
    {
        return [
            [['"attributeLabel" is not a valid file.'], ['tmp_name' => 'file', 'name' => 'name']],
        ];
    }
}
