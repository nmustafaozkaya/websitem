<?php

namespace Database\Seeders\Cinemas;

use Illuminate\Database\Seeder;
use App\Models\Cinema;
use App\Models\City;
use Faker\Factory as Faker;

class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎪 Sinemalar oluşturuluyor...');

        $faker = Faker::create('tr_TR');
        $cities = City::all();

        if ($cities->isEmpty()) {
            $this->command->error('❌ Önce şehirler oluşturulmalı! CitySeeder çalıştır.');
            return;
        }

        // Şehirlere göre gerçekçi sinema zinciri ve AVM eşleştirmesi
        $cityCinemaMapping = $this->getCityCinemaMapping();
        
        $totalCinemas = 0;

        foreach ($cities as $city) {
            $cityName = $city->name;
            
            // Şehir için sinema eşleştirmesi var mı kontrol et
            if (isset($cityCinemaMapping[$cityName])) {
                $cinemas = $cityCinemaMapping[$cityName];
            } else {
                // Eşleştirme yoksa varsayılan sinemalar
                $cinemas = [
                    ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                    ['chain' => 'Avşar Sinemaları', 'mall' => 'Optimum']
                ];
            }
            
            // Her şehirde sinemalar oluştur
            foreach ($cinemas as $cinemaData) {
                $chain = $cinemaData['chain'];
                $mall = $cinemaData['mall'];
                
                // Özel format kontrolü (Gaziantep için Avşar Sinema)
                if ($cityName === 'Gaziantep' && $chain === 'Avşar Sinema' && $mall === 'Sanko Park') {
                    $cinemaName = "{$chain} / {$cityName} {$mall}";
                } else {
                    $cinemaName = "{$chain} {$mall} {$cityName}";
                }
                
                Cinema::firstOrCreate([
                    'name' => $cinemaName,
                    'city_id' => $city->id
                ], [
                    'address' => "{$mall} AVM, Kat: {$faker->numberBetween(1, 3)}, {$cityName}",
                    'phone' => $this->generatePhoneNumber(),
                    'email' => $this->generateEmail($chain, $mall, $cityName)
                ]);

                $totalCinemas++;
            }
        }

        $this->command->info("✅ {$totalCinemas} sinema oluşturuldu.");
    }

    private function generatePhoneNumber(): string
    {
        return '0' . rand(500, 599) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999);
    }

    private function generateEmail(string $chain, string $mall, string $city): string
    {
        $email = strtolower(str_replace([' ', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], 
            ['.',  'i', 'g', 'u', 's', 'o', 'c'], 
            $chain . '.' . $mall . '.' . $city
        ));
        
        return $email . '@sinema.com';
    }

    /**
     * Şehirlere göre gerçekçi sinema zinciri ve AVM eşleştirmesi
     * Türkiye'deki gerçek sinema zincirleri ve AVM'ler baz alınarak oluşturuldu
     */
    private function getCityCinemaMapping(): array
    {
        return [
            // Büyükşehirler - Gerçekçi eşleştirmeler
            'İstanbul' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Kanyon'],
                ['chain' => 'Cinemarine', 'mall' => 'Akasya']
            ],
            'Ankara' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Prestige Sinemaları', 'mall' => 'Cevahir']
            ],
            'İzmir' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Palladium']
            ],
            
            // Akdeniz Bölgesi
            'Antalya' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Mall of Antalya'],
                ['chain' => 'Cinetime', 'mall' => 'TerraCity']
            ],
            'Mersin' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Mersin Park']
            ],
            'Adana' => [
                ['chain' => 'Cinemaximum', 'mall' => 'M1 Adana'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Optimum']
            ],
            'Hatay' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Hatay Park']
            ],
            'Osmaniye' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Osmaniye Park']
            ],
            
            // Güneydoğu Anadolu
            'Gaziantep' => [
                ['chain' => 'Paribu Cineverse', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinema', 'mall' => 'Sanko Park'],
                ['chain' => 'Paribu Cineverse', 'mall' => 'Forum']
            ],
            'Şanlıurfa' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Urfa City']
            ],
            'Diyarbakır' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Diyarbakır Park']
            ],
            'Mardin' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Mardin Park']
            ],
            'Batman' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Batman Park']
            ],
            'Siirt' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Siirt Park']
            ],
            'Şırnak' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Şırnak Park']
            ],
            'Hakkari' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Hakkari Park']
            ],
            'Kilis' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Kilis Park']
            ],
            'Adıyaman' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Adıyaman Park']
            ],
            'Kahramanmaraş' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Maraş Park']
            ],
            
            // Doğu Anadolu
            'Van' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Van Park']
            ],
            'Muş' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Muş Park']
            ],
            'Bitlis' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Bitlis Park']
            ],
            'Bingöl' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Bingöl Park']
            ],
            'Tunceli' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Tunceli Park']
            ],
            'Elazığ' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Elazığ Park']
            ],
            'Malatya' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Malatya Park']
            ],
            'Erzincan' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Erzincan Park']
            ],
            'Erzurum' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Erzurum Park'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Güzelyurt']
            ],
            'Ağrı' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Ağrı Park']
            ],
            'Kars' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Kars Park']
            ],
            'Ardahan' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Ardahan Park']
            ],
            'Iğdır' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Iğdır Park']
            ],
            'Artvin' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Artvin Park']
            ],
            
            // Karadeniz Bölgesi
            'Rize' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Rize Park']
            ],
            'Trabzon' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'NovaPark']
            ],
            'Giresun' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Giresun Park']
            ],
            'Ordu' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Ordu Park']
            ],
            'Samsun' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Yeşilyurt']
            ],
            'Amasya' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Amasya Park']
            ],
            'Tokat' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Tokat Park']
            ],
            'Sinop' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Sinop Park']
            ],
            'Kastamonu' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Kastamonu Park']
            ],
            'Zonguldak' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Zonguldak Park']
            ],
            'Bartın' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Bartın Park']
            ],
            'Karabük' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Karabük Park']
            ],
            'Düzce' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Düzce Park']
            ],
            'Bolu' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Bolu Park']
            ],
            
            // İç Anadolu
            'Sivas' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Sivas Park']
            ],
            'Yozgat' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Yozgat Park']
            ],
            'Nevşehir' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Nevşehir Park']
            ],
            'Kırşehir' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Kırşehir Park']
            ],
            'Kayseri' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Kayseri Park']
            ],
            'Niğde' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Niğde Park']
            ],
            'Aksaray' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Aksaray Park']
            ],
            'Karaman' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Karaman Park']
            ],
            'Konya' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Kulesite'],
                ['chain' => 'Cinepink', 'mall' => 'Konya City']
            ],
            'Çorum' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Çorum Park']
            ],
            'Çankırı' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Çankırı Park']
            ],
            'Kırıkkale' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Kırıkkale Park']
            ],
            'Eskişehir' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Espark'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Kanatlı']
            ],
            'Kütahya' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Kütahya Park']
            ],
            'Uşak' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Uşak Park']
            ],
            
            // Ege Bölgesi
            'Aydın' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinepink', 'mall' => 'Aydın Park']
            ],
            'Denizli' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinepink', 'mall' => 'Pamukkale']
            ],
            'Muğla' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Muğla Park']
            ],
            'Manisa' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinepink', 'mall' => 'Manisa Park']
            ],
            'Isparta' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinepink', 'mall' => 'Isparta Park']
            ],
            'Burdur' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Burdur Park']
            ],
            'Afyonkarahisar' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Afyon Park']
            ],
            
            // Marmara Bölgesi
            'Bursa' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Piazza'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Zafer Plaza']
            ],
            'Balıkesir' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Balıkesir Park'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Balıkesir Forum']
            ],
            'Bilecik' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Bilecik Park']
            ],
            'Çanakkale' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Çanakkale Park']
            ],
            'Edirne' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Edirne Park']
            ],
            'Kırklareli' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Kırklareli Park']
            ],
            'Tekirdağ' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Tekirdağ Park']
            ],
            'Sakarya' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Sakarya Park']
            ],
            'Kocaeli' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Ormanya']
            ],
            'Yalova' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Cinemarine', 'mall' => 'Yalova Park']
            ],
            
            // Diğer
            'Gümüşhane' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Gümüşhane Park']
            ],
            'Bayburt' => [
                ['chain' => 'Cinemaximum', 'mall' => 'Forum'],
                ['chain' => 'Avşar Sinemaları', 'mall' => 'Bayburt Park']
            ],
        ];
    }
}
