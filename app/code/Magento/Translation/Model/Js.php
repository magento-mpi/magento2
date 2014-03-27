<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translation\Model;

class Js 
{
    /**
     * Translation data
     *
     * @var string[]
     */
    protected $translateData;

    /**
     * @param Js\DataProviderInterface[] $dataProviders
     */
    public function __construct(array $dataProviders)
    {
        /** @var $dataProvider Js\DataProviderInterface */
        foreach ($dataProviders as $dataProvider) {
            foreach ($dataProvider->getData() as $key => $translatedText) {
                if ($key !== $translatedText) {
                    $this->translateData[$key] = $translatedText;
                }
            }
        }
    }

    /**
     * Get translated data
     *
     * @return string[]
     */
    public function getTranslateData()
    {
        return $this->translateData;
    }
} 
