<?php

namespace Typo3Api\Utility;


use Symfony\Component\Intl\Intl;

class IntlItemsProcFunc
{
    public function addCountryNames(&$params)
    {
        $countryNames = Intl::getRegionBundle()->getCountryNames('en');
        asort($countryNames);
        foreach ($countryNames as $countryCode => $countryName) {
            $params['items'][] = [$countryName, $countryCode];
        }
    }

    public function addLanguages(&$params)
    {
        $languageNames = Intl::getLanguageBundle()->getLanguageNames('en');
        asort($languageNames);
        foreach ($languageNames as $locale => $languageName) {
            $params['items'][] = [$languageName, $locale];
        }
    }
}