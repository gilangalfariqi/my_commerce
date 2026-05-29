<?php

namespace Database\Seeders;

use App\Models\BikeBrand;
use App\Models\BikeModel;
use App\Models\Product;
use App\Models\ProductFitment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder for MotoPartHub bike brand/model fitment data.
 *
 * Creates a realistic dataset of Indonesian motorcycle brands & models
 * and links them to existing products for testing the fitment filter system.
 */
class BikeFitmentSeeder extends Seeder
{
    /**
     * Motorcycle brand → model → years data.
     * Reflects the Indonesian market most popular models.
     */
    private array $bikeData = [
        'Honda' => [
            'country_of_origin' => 'Japan',
            'models' => [
                ['name' => 'Vario 125', 'cc' => '125cc', 'type' => 'SOHC', 'years' => [2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023]],
                ['name' => 'Vario 160', 'cc' => '160cc', 'type' => 'SOHC', 'years' => [2022, 2023, 2024]],
                ['name' => 'Beat Street', 'cc' => '108.2cc', 'type' => 'SOHC', 'years' => [2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023]],
                ['name' => 'PCX 160', 'cc' => '160cc', 'type' => 'SOHC', 'years' => [2021, 2022, 2023, 2024]],
                ['name' => 'CBR150R', 'cc' => '150cc', 'type' => 'DOHC', 'years' => [2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023]],
                ['name' => 'Supra X 125', 'cc' => '124.8cc', 'type' => 'SOHC', 'years' => [2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015]],
                ['name' => 'Revo X', 'cc' => '109.1cc', 'type' => 'SOHC', 'years' => [2016, 2017, 2018, 2019, 2020]],
            ],
        ],
        'Yamaha' => [
            'country_of_origin' => 'Japan',
            'models' => [
                ['name' => 'NMAX 155', 'cc' => '155cc', 'type' => 'SOHC', 'years' => [2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023]],
                ['name' => 'XMAX 250', 'cc' => '249cc', 'type' => 'SOHC', 'years' => [2017, 2018, 2019, 2020, 2021, 2022, 2023]],
                ['name' => 'Aerox 155', 'cc' => '155cc', 'type' => 'SOHC', 'years' => [2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023]],
                ['name' => 'Mio M3', 'cc' => '113.7cc', 'type' => 'SOHC', 'years' => [2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020]],
                ['name' => 'MT-25', 'cc' => '249cc', 'type' => 'DOHC', 'years' => [2015, 2016, 2017, 2018, 2019, 2020, 2021]],
                ['name' => 'Vixion R', 'cc' => '155cc', 'type' => 'DOHC', 'years' => [2017, 2018, 2019, 2020, 2021, 2022, 2023]],
            ],
        ],
        'Kawasaki' => [
            'country_of_origin' => 'Japan',
            'models' => [
                ['name' => 'Ninja 250 SL', 'cc' => '249cc', 'type' => 'DOHC', 'years' => [2015, 2016, 2017]],
                ['name' => 'KLX 150', 'cc' => '144cc', 'type' => 'SOHC', 'years' => [2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021]],
                ['name' => 'Ninja ZX-25R', 'cc' => '249cc', 'type' => 'DOHC', 'years' => [2020, 2021, 2022, 2023]],
                ['name' => 'W175', 'cc' => '177cc', 'type' => 'SOHC', 'years' => [2017, 2018, 2019, 2020, 2021, 2022]],
            ],
        ],
        'Suzuki' => [
            'country_of_origin' => 'Japan',
            'models' => [
                ['name' => 'GSX-R150', 'cc' => '150cc', 'type' => 'DOHC', 'years' => [2017, 2018, 2019, 2020, 2021]],
                ['name' => 'Satria F150', 'cc' => '147.3cc', 'type' => 'DOHC', 'years' => [2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020]],
                ['name' => 'Address 110', 'cc' => '113cc', 'type' => 'SOHC', 'years' => [2015, 2016, 2017, 2018, 2019, 2020]],
            ],
        ],
    ];

    public function run(): void
    {
        $this->command->info('🏍️  Seeding bike brands & models...');

        $createdModels = [];

        foreach ($this->bikeData as $brandName => $brandData) {
            $brand = BikeBrand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name'               => $brandName,
                    'country_of_origin'  => $brandData['country_of_origin'],
                    'is_active'          => true,
                    'sort_order'         => array_search($brandName, array_keys($this->bikeData)),
                ]
            );

            foreach ($brandData['models'] as $modelData) {
                $model = BikeModel::firstOrCreate(
                    ['slug' => Str::slug($brandName . '-' . $modelData['name'])],
                    [
                        'bike_brand_id'  => $brand->id,
                        'name'           => $modelData['name'],
                        'engine_cc'      => $modelData['cc'],
                        'engine_type'    => $modelData['type'],
                        'years_available' => $modelData['years'],
                        'is_active'      => true,
                        'sort_order'     => 0,
                    ]
                );

                $createdModels[] = $model;
            }

            $this->command->line("  ✓ {$brandName} — " . count($brandData['models']) . ' models');
        }

        // Attach random fitments to existing products for testing
        $products = Product::all();
        if ($products->isNotEmpty()) {
            $this->command->info('🔧 Linking fitments to existing products...');
            foreach ($products as $product) {
                $sampleModels = collect($createdModels)->random(min(3, count($createdModels)));
                foreach ($sampleModels as $model) {
                    $years = $model->years_available ?? [];
                    $year  = ! empty($years) ? $years[array_rand($years)] : null;

                    ProductFitment::firstOrCreate([
                        'product_id'    => $product->id,
                        'bike_model_id' => $model->id,
                        'year'          => $year,
                    ]);
                }
            }
            $this->command->line('  ✓ Fitments linked to ' . $products->count() . ' products');
        }

        $this->command->info('✅ BikeFitmentSeeder completed!');
    }
}
