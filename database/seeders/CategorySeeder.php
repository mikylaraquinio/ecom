<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {

        // === Main Category: Grains & Cereals ===
        $grainsId = DB::table('categories')->insertGetId([
            'name' => 'Grains & Cereals',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subcategories = [
            'Cereal Grains' => [
                'Barley',
                'Finger Millet',
                'Foxtail Millet',
                'Job’s Tears',
                'Japanese Millet',
                'Kodo Millet',
                'Maize (Corn)',
                'Millet',
                'Oats',
                'Pearl Millet',
                'Proso Millet',
                'Rice',
                'Rye',
                'Sorghum',
                'Spelt',
                'Triticale',
                'Teff',
                'Wheat',
                'Wild Rice',
            ],
            'Pseudocereal Grains' => [
                'Amaranth',
                'Buckwheat',
                'Quinoa',
                'Chia',
                'Canary Seed',
                'Kañiwa',
            ],
        ];

        foreach ($subcategories as $subcategory => $items) {
            $subCatId = DB::table('categories')->insertGetId([
                'name' => $subcategory,
                'parent_id' => $grainsId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertData = [];
            foreach ($items as $item) {
                $insertData[] = [
                    'name' => $item,
                    'parent_id' => $subCatId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('categories')->insert($insertData);
        }

        // === Main Category: Vegetables ===
        $vegetablesId = DB::table('categories')->insertGetId([
            'name' => 'Vegetables',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subcategories = [
            'Brassicas (Cabbage Family)' => [
                'Broccoli (Calabrese)',
                'Broccoflower (Hybrid)',
                'Brussels Sprouts',
                'Cabbage',
                'Cauliflower',
                'Kohlrabi',
                'Red Cabbage'
            ],
            'Fruiting Vegetables' => [
                'Banana Squash',
                'Bell Pepper',
                'Butternut Squash',
                'Cayenne Pepper',
                'Chilli Pepper',
                'Cube Pumpkin',
                'Cucumber',
                'Delicata Squash',
                'Eggplant (Aubergine)',
                'Gem Squash',
                'Habanero',
                'Hubbard Squash',
                'Jalapeño',
                'Paprika',
                'Pumpkin',
                'Squash (Marrow, UK)',
                'Spaghetti Squash',
                'Sweetcorn (Corn)',
                'Tomato',
                'Watermelon (Can also go under Fruits)',
                'Zucchini (Courgette)'
            ],
            'Leafy Greens' => [
                'Arugula',
                'Bok Choy',
                'Collard Greens',
                'Cress',
                'Endive (Frisee)',
                'Kale',
                'Lettuce',
                'Mustard Greens',
                'Nettles',
                'New Zealand Spinach',
                'Savoy Cabbage',
                'Spinach',
                'Swiss Chard (Beet Greens)',
                'Tat Soi',
                'Watercress'
            ],
            'Legumes' => [
                'Alfalfa Sprouts',
                'Bean Sprouts',
                'Chickpeas',
                'Green Beans',
                'Peas',
                'Runner Beans',
                'Snap Peas (Mange Tout)',
                'Soybean',
                'Yellow Pea'
            ],
            'Mushrooms & Fungi' => [
                'Mushroom',
                'Oyster Mushroom'
            ],
            'Other & Specialty Vegetables' => [
                'Artichoke',
                'Asparagus',
                'Celery',
                'Chives',
                'Cube Watermelon (unique)',
                'Fiddleheads',
                'Okra',
                'Rhubarb',
                'Wasabi',
                'Water Chestnut'
            ],
            'Roots & Tubers' => [
                'Beetroot (Beet)',
                'Celeriac',
                'Daikon (White Radish)',
                'Eddoe',
                'Fennel',
                'Garlic',
                'Ginger',
                'Horseradish',
                'Jicama',
                'Jerusalem Artichoke',
                'Konjac',
                'Leek',
                'Onion',
                'Parsnip',
                'Potato',
                'Radish',
                'Rutabaga (Swede)',
                'Salsify (Oyster Plant)',
                'Scallion (Spring Onion, Green Onion)',
                'Shallot',
                'Skirret',
                'Sweet Potato',
                'Taro',
                'Turnip',
                'Yam'
            ],
        ];

        foreach ($subcategories as $subcategory => $items) {
            $subCatId = DB::table('categories')->insertGetId([
                'name' => $subcategory,
                'parent_id' => $vegetablesId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertData = [];
            foreach ($items as $item) {
                $insertData[] = [
                    'name' => $item,
                    'parent_id' => $subCatId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('categories')->insert($insertData);
        }

        // Main Category: Fruits
        $fruitsId = DB::table('categories')->insertGetId([
            'name' => 'Fruits',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subcategories and Items
        $subcategories = [
            'Citrus Fruits' => [
                'Buddha’s Hand (Fingered Citron)',
                'Finger Lime (Caviar Lime)',
                'Grapefruit',
                'Kaffir Lime',
                'Lemon',
                'Lime',
                'Mandarin',
                'Orange',
                'Pomelo',
                'Satsuma',
                'Tangelo',
                'Ugli Fruit',
                'Yuzu'
            ],
            'Berries' => [
                'Açaí',
                'Bilberry',
                'Blackberry',
                'Blackcurrant',
                'Blueberry',
                'Boysenberry',
                'Cloudberry',
                'Cranberry',
                'Currant',
                'Elderberry',
                'Goji Berry',
                'Gooseberry',
                'Honeyberry',
                'Huckleberry',
                'Jostaberry',
                'Juniper Berry',
                'Lingonberry',
                'Loganberry',
                'Marionberry',
                'Mulberry',
                'Phalsa (Falsa)',
                'Raspberry',
                'Redcurrant',
                'Salal Berry',
                'Salmonberry',
                'Strawberry',
                'Tayberry',
                'Thimbleberry',
                'White Currant'
            ],
            'Melons' => [
                'Cantaloupe',
                'Galia Melon',
                'Horned Melon (Kiwano)',
                'Honeydew Melon',
                'Musk Melon',
                'Watermelon'
            ],
            'Tropical & Exotic Fruits' => [
                'Abiu',
                'Ackee',
                'Araza',
                'Aratiles',
                'Atemoya',
                'Babaco',
                'Bael',
                'Banana',
                'Barbadine',
                'Biriba',
                'Black Sapote',
                'Breadfruit',
                'Cacao',
                'Caimito (Star Apple)',
                'Canistel (Egg Fruit)',
                'Carambola (Star Fruit)',
                'Catmon',
                'Cempedak',
                'Cherimoya (Custard Apple)',
                'Chico (Sapodilla)',
                'Coconut',
                'Cupuaçu',
                'Durian',
                'Feijoa',
                'Gac Fruit (Baby Jackfruit)',
                'Guava',
                'Guyabano (Soursop)',
                'Hala Fruit',
                'Ilama',
                'Imbu',
                'Jackfruit',
                'Jambul',
                'Jatoba',
                'Jujube',
                'Kaffir Lime',
                'Kiwano (Horned Melon)',
                'Kumquat',
                'Langsat',
                'Lanzones',
                'Longan',
                'Loquat',
                'Lucuma',
                'Lychee',
                'Mamey Apple',
                'Mamey Sapote',
                'Mangosteen',
                'Miracle Fruit',
                'Nance',
                'Noni',
                'Papaya',
                'Passion Fruit',
                'Pawpaw',
                'Persimmon',
                'Pineapple',
                'Pitanga (Surinam Cherry)',
                'Pitaya (Dragon Fruit)',
                'Plantain',
                'Pulasan',
                'Rambutan',
                'Rose Apple',
                'Safou (Butterfruit)',
                'Salak (Snake Fruit)',
                'Santol',
                'Sapote',
                'Soursop (Guyabano)',
                'Star Apple (Caimito)',
                'Star Fruit (Carambola)',
                'Sugar Apple (Atis)',
                'Tamarillo',
                'Tamarind',
                'Velvet Apple (Mabolo)',
                'White Sapote',
                'Yangmei'
            ],
            'Stone Fruits (Drupes)' => [
                'Apricot',
                'Cherry',
                'Damson',
                'Mango',
                'Nectarine',
                'Olive',
                'Peach',
                'Plum',
                'Plumcot (Pluot)'
            ],
            'Pome Fruits' => [
                'Apple',
                'Crab Apple',
                'Loquat',
                'Medlar',
                'Pear',
                'Quince'
            ],
            'Figs & Related Fruits' => [
                'Fig',
                'Osage Orange'
            ],
            'Vine Fruits' => [
                'Grape',
                'Kiwifruit',
                'Passion Fruit'
            ],
            'Nutritious & Super Fruits' => [
                'Açaí',
                'Avocado',
                'Camu Camu',
                'Cranberry',
                'Goji Berry',
                'Pomegranate'
            ],
            'Unique/Other Fruits' => [
                'African Cherry Orange',
                'American Mayapple',
                'Coco de Mer',
                'Fiddleheads',
                'Hala Fruit',
                'Magellan Barberry',
                'Momordica Fruit',
                'Mouse Melon',
                'Red Poppy (for culinary seeds)',
                'Saquico',
                'Sarguelas (Red Mombin)',
                'Swiss Cheese Plant Fruit',
                'Ximenia'
            ],
        ];

        foreach ($subcategories as $subcategory => $items) {
            $subCatId = DB::table('categories')->insertGetId([
                'name' => $subcategory,
                'parent_id' => $fruitsId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertData = [];
            foreach ($items as $item) {
                $insertData[] = [
                    'name' => $item,
                    'parent_id' => $subCatId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('categories')->insert($insertData);
        }

        // === Main Category: Herbs & Spices ===
        $herbsSpicesId = DB::table('categories')->insertGetId([
            'name' => 'Herbs & Spices',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subcategories = [
            'Seeds & Pods' => [
                'Cumin',
                'Coriander Seed',
                'Caraway',
                'Fennel Seed',
                'Fenugreek',
                'Nigella (Black Caraway or Kalonji)',
                'Sesame Seed',
                'Celery Seed',
                'Dill Seed',
                'Mustard (Black, Brown, White)',
                'Cardamom (Green, Black, Ethiopian)',
                'Aniseed (Anise)',
                'Ajwain (Carom Seeds)',
                'Chironji (Charoli)',
                'Poppy Seed',
                'Lovage Seeds',
                'Perilla Seeds (Deulkkae)',
                'Muskmallow Seeds',
                'Quassia',
                'Grains of Paradise',
                'Grains of Selim',
                'Juniper Berry',
                'Pink Pepper',
                'Alligator Pepper (Mbongo Spice)',
            ],
            'Roots, Rhizomes, & Bark' => [
                'Cinnamon',
                'Cassia',
                'Asafoetida',
                'Ginger',
                'Turmeric',
                'Horseradish',
                'Greater Galangal',
                'Lesser Galangal',
                'Fingerroot (Chinese Ginger)',
                'Kencur',
                'Licorice',
                'Orris Root',
                'Spikenard',
            ],
            'Fruits, Berries & Peppers' => [
                'Black Pepper (Black, White, Green)',
                'Long Pepper',
                'Sichuan Pepper',
                'Dorrigo Pepper',
                'Mountain Pepper (Cornish Pepper Leaf, Tasmanian Pepper)',
                'Aleppo Pepper',
                'Cayenne Pepper',
                'Chili Pepper',
                'Jalapeño',
                'New Mexico Chile',
                'Paprika',
                'Peruvian Pepper',
                'Passion Berry',
            ],
            'Floral & Other Exotic Spices' => [
                'Star Anise',
                'Clove',
                'Mace',
                'Nutmeg',
                'Saffron',
                'Annatto',
                'Sumac',
                'Za’atar',
                'Kewra (Pandan Flower Extract)',
                'Vanilla',
                'Tonka Beans',
                'Voatsiperifery',
                'Dootsi',
                'Kodampuli (Malabar Tamarind)',
                'Kokam',
                'Mahleb',
                'Kutjura',
                'Njangsa',
                'Mountain Horopito',
                'Golpar',
                'Carob (Locust Beans)',
                'Blue Fenugreek',
                'Mugwort (Artemisia)',
                'Barberry',
                'Alkanet (edible dye)',
            ],
            'Leafy & Aromatic Herbs' => [
                'Basil (Sweet, Holy)',
                'Mint (Spearmint, Peppermint)',
                'Oregano (Regular, Mexican, Cuban)',
                'Thyme (Regular, Wild, Lemon)',
                'Rosemary',
                'Sage',
                'Parsley',
                'Cilantro (Coriander Leaves)',
                'Chives (Garlic Chives)',
                'Marjoram',
                'Tarragon',
                'Lovage Leaves',
                'Dill Weed',
                'Watercress',
                'Purslane',
                'Salad Burnet',
                'Borage',
                'Wintergreen',
                'Sweet Woodruff',
                'Vietnamese Balm (Kinh Gioi)',
            ],
            'Bay Leaves' => [
                'Bay Leaf (Regular, Indian, Indonesian, Mexican, West Indian)',
            ],
            'Floral & Unique Herbs' => [
                'Lavender',
                'Chamomile',
                'Elderflower',
                'Jasmine Flowers',
                'Clary (Clary Sage)',
                'Alexanders',
                'Angelica',
                'Avens',
                'Sassafras',
                'Alkanet',
                'Woodruff',
                'Yarrow',
                'Sweet Woodruff',
                'Wood Avens (Herb Bennet)',
            ],
            'Medicinal & Traditional Herbs' => [
                'Culantro',
                'Curry Leaf',
                'Lemon Balm',
                'Lemon Verbena',
                'Hyssop',
                'Chicory',
                'Rue',
                'Stone Parsley',
                'Sheep Sorrel',
                'Sorrel',
            ],
            'Asian & African Specialty Herbs' => [
                'Lemongrass',
                'Vietnamese Coriander',
                'Kaffir Lime Leaves',
                'Huacatay',
                'Jimbu',
                'Hoja Santa',
                'Epazote',
                'Fish Mint',
                'Pandan Leaf (Screwpine)',
                'Kkaennip (Perilla Frutescens Leaves)',
                'Shiso',
                'Pipicha (Straight-leaf Pápalo)',
                'Pápalo',
                'Dootsi',
                'Koseret Leaves',
                'Filé Powder (Gumbo Filé)',
                'Lesser Calamint',
                'Lemon Myrtle',
                'Lemon Ironbark',
            ],
            'Other Specialty Herbs' => [
                'Mastic',
                'Costmary',
                'Boldo',
                'Avocado Leaf (non-toxic varieties)',
                'Safflower',
                'Smartweed (Water Pepper)',
            ],
        ];

        foreach ($subcategories as $subcategory => $items) {
            $subCatId = DB::table('categories')->insertGetId([
                'name' => $subcategory,
                'parent_id' => $herbsSpicesId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertData = [];
            foreach ($items as $item) {
                $insertData[] = [
                    'name' => $item,
                    'parent_id' => $subCatId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('categories')->insert($insertData);
        }

    }
}
