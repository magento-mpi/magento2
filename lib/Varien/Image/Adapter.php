<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Image_Adapter
{
    const ADAPTER_GD    = 'GD';
    const ADAPTER_GD2   = 'GD2';
    const ADAPTER_IM    = 'IMAGEMAGICK';
    const ADAPTER_IME   = 'IMAGEMAGICK_EXTERNAL';

    public static function factory($adapter)
    {
        switch( $adapter ) {
            case self::ADAPTER_GD:
                return new Varien_Image_Adapter_Gd();
                break;

            case self::ADAPTER_GD2:
                return new Varien_Image_Adapter_Gd2();
                break;

            case self::ADAPTER_IM:
                return new Varien_Image_Adapter_ImageMagick();
                break;

            case self::ADAPTER_IME:
                return new Varien_Image_Adapter_ImageMagickExternal();
                break;

            default:
                throw new Exception('Invalid adapter selected.');
                break;
        }
    }
}
