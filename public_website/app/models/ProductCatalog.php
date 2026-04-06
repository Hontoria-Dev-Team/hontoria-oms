<?php
/**
 * ProductCatalog.php
 * Single Responsibility: defines what products exist and their metadata.
 * Open/Closed: add new products here without touching any other file.
 *
 * COLUMN ORDER:
 *   [ id, name, category, description, price, icon, bgGradient, coverPhoto ]
 *
 * HOW TO ADD A NEW PRODUCT:
 *   1. Add one row in the correct category section below.
 *   2. Add its photos in PhotoLibrary.php
 *   3. Add its variants (if any) in VariantLibrary.php
 *
 * HOW TO SWITCH TO A DATABASE LATER:
 *   Replace the definitions() method body with a DB query
 *   that returns rows in the same 8-column format.
 */
class ProductCatalog {

    public static function definitions(): array {
        return [

            // ── SUBLIMATION ───────────────────────────────────────────────
            ['jersey',               'Jersey',               'sublimation', 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',       300,  'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/jerseyPicture/jerseyPicture1.jpg'  ],
            ['tshirt',               'T-Shirt',              'sublimation', 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',                                  300,  'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/tshirtPicture/tshirtPicture1.jpg'  ],
            ['short',                'Short',                'sublimation', 'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.',                                   300,  'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/shortPicture/short1.jpg'           ],
            ['warmer',               'Warmer',               'sublimation', 'Sublimation warmers for players and athletes. Keeps you warm while looking professional.',                                              0,    'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/warmerPicture/warmer1.jpg'         ],
            ['joggingpants',         'Jogging Pants',        'sublimation', 'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching.',                                              450,  'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/pantsPicture/pants1.jpg'           ],
            ['longsleeve',           'Long Sleeve',          'sublimation', 'Full sublimation long sleeve shirts with vibrant custom designs. Perfect for teams, events, and everyday wear.',                        400,  'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/longsleveePicture/longslevee1.jpg' ],
            ['poloshirt',            'Polo Shirt',           'sublimation', 'Custom sublimation polo shirts with full color printing. Great for corporate events, teams, and casual wear.',                         430,  'fa-tshirt',        'linear-gradient(135deg,#fff5cc,#ffe57a)', 'img/sublimationPicture/poloshirtPicture/poloshirt1.jpg'   ],

            // ── UNIFORM ───────────────────────────────────────────────────
            ['school-uniform',       'School Uniform',       'uniform',     'Custom-made school uniforms tailored to your school\'s specifications. Durable, comfortable, and neat.',                              0,    'fa-user-graduate', 'linear-gradient(135deg,#e8f0ff,#c8d8ff)', ''],
            ['office-uniform',       'Office Uniform',       'uniform',     'Professional office uniforms tailored for a sharp, consistent look across your entire team.',                                         0,    'fa-briefcase',     'linear-gradient(135deg,#e8f0ff,#c8d8ff)', ''],
            ['professional-uniform', 'Professional Uniform', 'uniform',     'High-quality professional uniforms for healthcare, hospitality, and other industries.',                                               0,    'fa-user-tie',      'linear-gradient(135deg,#e8f0ff,#c8d8ff)', ''],

            // ── TARPAULIN ─────────────────────────────────────────────────
            ['birthday',             'Birthday Tarpaulin',       'tarpaulin', 'Beautiful custom birthday tarpaulins. Any size, any design — bold and colorful.',                                                   0,    'fa-birthday-cake', 'linear-gradient(135deg,#ffe0e0,#ffb3b3)', 'img/tarpaulin/birthdayTarpaulin/bday1.jpg'               ],
            ['congratulation',       'Congratulation Tarpaulin', 'tarpaulin', 'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.',                                0,    'fa-star',          'linear-gradient(135deg,#ffe0e0,#ffb3b3)', 'img/tarpaulin/congratulationTarpaulin/grats1.jpg'        ],

            // ── MUGS & TUMBLER ────────────────────────────────────────────
            ['mug',                  'Mug',                  'mug',         'Full-wrap sublimation printed mugs with your custom design. Perfect for gifts, souvenirs, and corporate giveaways.',                 150,  'fa-mug-hot',       'linear-gradient(135deg,#fff3e0,#ffe0b2)', 'img/sublimationMug/mug/mug1.jpg'                         ],
            ['tumbler',              'Tumbler',              'mug',         'Custom sublimation printed tumblers. Keep your drinks hot or cold while showing off your unique design.',                             350,  'fa-mug-hot',       'linear-gradient(135deg,#fff3e0,#ffe0b2)', 'img/sublimationMug/tumbler/tumbler1.jpg'                  ],

            // ── LANYARD ───────────────────────────────────────────────────
            ['lanyard',              'Lanyard',              'lanyard',     'Custom printed lanyards with your logo and colors. Durable and comfortable.',                                                         60,   'fa-id-card',       'linear-gradient(135deg,#f3e5f5,#e1bee7)', 'img/idLanyards/lanyard/lanyard1.jpg'                     ],

            // ── STITCHING ─────────────────────────────────────────────────
            ['stitching-tshirt',     'Customize Stitching',  'stitching',   'Tailored t-shirts with custom stitching and embroidery. Perfect for teams, events, and branded apparel.',                             0,    'fa-cut',           'linear-gradient(135deg,#e8f5e9,#c8e6c9)', 'img/customStitching/tshirt/stitching1.jpg'                ],

            // ── STICKERS & DECALS ─────────────────────────────────────────
            ['motorcycle-decal',     'Motorcycle Decals',    'sticker',     'High-quality waterproof motorcycle decals in any shape and design. Weather-resistant and long-lasting.',                              0,    'fa-motorcycle',    'linear-gradient(135deg,#fce4ec,#f8bbd0)', 'img/sticker_decals/motorDecals/motor1.jpg'                ],
            ['truck-decal',          'Truck Decals',         'sticker',     'Large-format truck decals and vinyl wraps. Bold, vibrant, and built to withstand the elements.',                                      0,    'fa-truck',         'linear-gradient(135deg,#fce4ec,#f8bbd0)', 'img/sticker_decals/truckDecals/truck1.jpg'                ],
            ['car-decal',            'Car Decals',           'sticker',     'Custom car decals and stickers. Perfect for business branding, personal style, or promotional use.',                                  0,    'fa-car',           'linear-gradient(135deg,#fce4ec,#f8bbd0)', 'img/sticker_decals/carDecals/car1.jpg'                    ],

            // ── SINTRA BOARD ──────────────────────────────────────────────
            ['sintra-board',         'Sintra Board',         'sintra',      'Custom printed sintra boards for signage, displays, and advertising. Lightweight, durable, and weather-resistant.',                   0,    'fa-border-all',    'linear-gradient(135deg,#e3f2fd,#bbdefb)', 'img/sintraBoard/board/board1.jpg'                        ],

            // ── PHOTO FRAME ───────────────────────────────────────────────
            ['photo-frame',          'Photo Frame',          'photoframe',  'Custom sublimation printed photo frames. Perfect for gifts, events, and keepsakes. Available in various sizes.',                      0,    'fa-image',         'linear-gradient(135deg,#f3e5f5,#e1bee7)', 'img/photoFrame/frame/frame1.jpg'                         ],

            // ── REF MAGNET ────────────────────────────────────────────────
            ['ref-magnet',           'Ref Magnet',           'refmagnet',   'Personalized refrigerator magnets with custom designs. Great for souvenirs, giveaways, and promotional items.',                       35,   'fa-magnet',        'linear-gradient(135deg,#e8f5e9,#c8e6c9)', 'img/refMagnet/magnet/magnet1.jpg'                        ],

            // ── PLAQUE & MEDAL ────────────────────────────────────────────
            ['plaque',               'Plaque and Trophies',  'plaque',      'Custom engraved plaques for awards, recognition, and achievements. Professional finish with personalized text and design.',            0,    'fa-award',         'linear-gradient(135deg,#fff8e1,#ffecb3)', 'img/plaque_medal/plaque/plaque1.jpg'                     ],
            ['medal',                'Medal',                'plaque',      'Complete set medal with ribbon (6 cm). Perfect for sports events, competitions, and recognition ceremonies. Available in gold, silver, and bronze.', 50, 'fa-medal', 'linear-gradient(135deg,#fff8e1,#ffecb3)', 'img/plaque_medal/medal/medal1.jpg'],

        ];
    }
}
?>