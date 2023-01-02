<?php

namespace App\Enums;

use Illuminate\Support\Collection;
use League\ISO3166\ISO3166;
use League\ISO3166\ISO3166DataProvider;

enum Market: string
{
    case Random = 'random';
    case HighGDP = 'high_gdp';
    case LowGDP = 'low_gdp';
    case Scandinavia = 'scandinavia';

    /**
     * @return Collection<int, string>
     */
    public function iso2(int $limit = 10): Collection
    {
        return match ($this) {
            self::Random => $this->random($limit),
            self::HighGDP => $this->highGDP($limit),
            self::LowGDP => $this->lowGDP($limit),
            self::Scandinavia => $this->scandinavia($limit),
        };
    }

    /**
     * @return Collection<int, string>
     */
    private function random(int $limit): Collection
    {
        $codes = (new ISO3166())->iterator();

        return Collection::make(iterator_to_array($codes))
            ->random($limit)
            ->map(fn(array $country) => $country['alpha2'])
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    public function highGDP(int $limit): Collection
    {
        $codes = (new ISO3166())->iterator();

        $richCountries = [
            'AE',
            'AT',
            'AU',
            'BE',
            'BG',
            'BH',
            'BN',
            'CA',
            'CH',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GB',
            'GR',
            'HK',
            'HR',
            'HU',
            'IE',
            'IL',
            'IS',
            'IT',
            'JP',
            'KR',
            'KW',
            'LT',
            'LU',
            'LV',
            'MO',
            'MT',
            'NL',
            'NO',
            'NZ',
            'PL',
            'PT',
            'QA',
            'RO',
            'RS',
            'SA',
            'SE',
            'SG',
            'SI',
            'SK',
            'TR',
            'TW',
            'US',
        ];

        return Collection::make(iterator_to_array($codes))
            ->whereIn('alpha2', $richCountries)
            ->random($limit)
            ->values()
            ->map(fn(array $country) => $country['alpha2']);
    }

    /**
     * @return Collection<int, string>
     */
    private function lowGDP(int $limit): Collection
    {
        $codes = (new ISO3166())->iterator();

        $poorCountries = [
            'AF',
            'AM',
            'AO',
            'BD',
            'BF',
            'BF',
            'BI',
            'BJ',
            'BO',
            'CD',
            'CF',
            'CG',
            'CI',
            'CM',
            'ER',
            'ET',
            'GA',
            'GH',
            'GN',
            'GT',
            'GW',
            'HN',
            'HT',
            'KG',
            'KG',
            'KH',
            'LA',
            'LR',
            'MG',
            'ML',
            'MW',
            'MZ',
            'NE',
            'NG',
            'NP',
            'PG',
            'PK',
            'RW',
            'SL',
            'SN',
            'SS',
            'TD',
            'TG',
            'TJ',
            'TJ',
            'UA',
            'UG',
            'YE',
            'ZM',
            'ZW',
        ];

        return Collection::make(iterator_to_array($codes))
            ->whereIn('alpha2', $poorCountries)
            ->random($limit)
            ->values()
            ->map(fn(array $country) => $country['alpha2']);
    }

    /**
     * @return Collection<int, string>
     */
    private function scandinavia(int $limit): Collection
    {
        $codes = (new ISO3166())->iterator();

        $scandinavianCountries = [
            'DK',
            'FI',
            'IS',
            'NO',
            'SE',
        ];

        return Collection::make(iterator_to_array($codes))
            ->whereIn('alpha2', $scandinavianCountries)
            ->random($limit)
            ->values()
            ->map(fn(array $country) => $country['alpha2']);
    }
}
