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
     * @param string|int|bool|null $value
     * @param bool $isAjax
     * @return Image
     */
    protected function getClass($value, $isAjax)
    {
        $imageForm = $this->getMock(
            'Magento\Customer\Model\Metadata\Form\Image',
            array('_isUploadedFile'),
            array(
                $this->localeMock,
                $this->loggerMock,
                $this->attributeMetadataMock,
                $this->localeResolverMock,
                $value,
                0,
                $isAjax,
                $this->coreDataMock,
                $this->fileValidatorMock,
                $this->fileSystemMock,
                $this->uploaderFactoryMock
            )
        );
        return $imageForm;
    }

    public function validateValueToUploadDataProvider()
    {
        $imagePath = __DIR__ . '/_files/logo.gif';
        return array(
            array(
                array('"realFileName" is not a valid file.'),
                array('tmp_name' => 'tmp_file', 'name' => 'realFileName'),
                array('valid' => false)
            ),
            array(true, array('tmp_name' => $imagePath, 'name' => 'logo.gif'))
        );
    }
}
