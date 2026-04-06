<?php
/**
 * VariantLibrary.php
 * Single Responsibility: defines price variants per product.
 *
 * HOW TO ADD VARIANTS FOR A NEW PRODUCT:
 *   - Add a new key matching the product id from ProductCatalog.php
 *   - Each variant needs a 'name' and a 'price'
 *   - Products with no variants get an empty array automatically
 */
class VariantLibrary {

    public static function build(): array {
        return [

            'mug' => [
                ['name' => 'White Mug',  'price' => 150],
                ['name' => 'Magic Mug',  'price' => 200],
            ],

            'birthday' => [
                ['name' => '2x3 ft.',    'price' =>  72],
                ['name' => '3x4 ft.',    'price' => 144],
                ['name' => '3x5 ft.',    'price' => 180],
                ['name' => '4x6 ft.',    'price' => 288],
                ['name' => '4x8 ft.',    'price' => 384],
            ],

            'congratulation' => [
                ['name' => '2x3 ft.',    'price' =>  72],
                ['name' => '3x4 ft.',    'price' => 144],
                ['name' => '3x5 ft.',    'price' => 180],
                ['name' => '4x6 ft.',    'price' => 288],
                ['name' => '4x8 ft.',    'price' => 384],
            ],

            'sintra-board' => [
                ['name' => 'A4 (flat)',              'price' =>  150],
                ['name' => 'A4 (box)',               'price' =>  200],
                ['name' => '18x24 inches (flat)',    'price' =>  350],
                ['name' => '18x24 inches (box)',     'price' =>  450],
                ['name' => '2x3 ft. (flat)',         'price' =>  500],
                ['name' => '2x3 ft. (box)',          'price' =>  750],
                ['name' => '3x4 ft. (flat)',         'price' => 1000],
                ['name' => '3x4 ft. (box)',          'price' => 1500],
            ],

            'plaque' => [
                ['name' => 'Acrylic Plaque - Small',            'price' =>  350],
                ['name' => 'Acrylic Plaque - Medium',           'price' =>  400],
                ['name' => 'Acrylic Plaque - Large',            'price' =>  500],
                ['name' => 'Acrylic Plaque - XL',               'price' =>  650],
                ['name' => 'Glass Plaque - Standard thickness', 'price' =>  400],
                ['name' => 'Glass Plaque - Thick',              'price' =>  800],
            ],

        ];
    }
}
?>