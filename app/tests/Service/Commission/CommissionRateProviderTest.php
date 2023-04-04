<?php

declare(strict_types=1);

namespace App\Tests\Service\Commission;

use App\Dto\CardMetadata;
use App\Dto\Country;
use App\Dto\Transaction;
use App\Service\Card\Metadata\MetadataProviderInterface;
use App\Service\Commission\CommissionRateProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommissionRateProviderTest extends TestCase
{
    private MetadataProviderInterface $metadataProvider;
    private CommissionRateProvider $commissionRateProvider;

    protected function setUp(): void
    {
        $this->metadataProvider = $this->createMock(MetadataProviderInterface::class);
        $this->commissionRateProvider = new CommissionRateProvider($this->metadataProvider);
    }

    public function testGetRateForNonEUCountry(): void
    {
        $transaction = new Transaction('123', '100.00', 'EUR');
        $cardMetadada = new CardMetadata(new Country('US'));

        $this->metadataProvider->method('get')->with(123)->willReturn($cardMetadada);

        $this->assertEquals(CommissionRateProvider::RATE_NON_EU, $this->commissionRateProvider->getRate($transaction));
    }

    #[DataProvider('getEUCountryCodes')]
    public function testGetRateForEUCountry(string $country): void
    {
        $transaction = new Transaction('123', '100.00', 'EUR');
        $cardMetadada = new CardMetadata(new Country($country));

        $this->metadataProvider->method('get')->with(123)->willReturn($cardMetadada);

        $this->assertEquals(CommissionRateProvider::RATE_EU, $this->commissionRateProvider->getRate($transaction));
    }

    public static function getEUCountryCodes(): array
    {
        return [
            ['AT'], ['BE'], ['BG'], ['HR'], ['CY'],
            ['CZ'], ['DK'], ['EE'], ['FI'], ['ES'],
            ['FR'], ['DE'], ['GR'], ['HU'], ['IE'],
            ['IT'], ['LV'], ['LT'], ['LU'], ['MT'],
            ['NL'], ['PL'], ['PT'], ['RO'], ['SK'],
            ['SI'], ['ES'], ['SE'],
        ];
    }
}
