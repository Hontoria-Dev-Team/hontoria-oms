<?php
/**
 * PhotoLibrary.php
 * Single Responsibility: builds photo arrays for each product.
 *
 * HOW TO ADD PHOTOS FOR A NEW PRODUCT:
 *   - Sequential files: use self::sequence('path/prefix', from, to)
 *   - Irregular files:  use self::manual([...paths...])
 *   - Key must match the product id in ProductCatalog.php
 */
class PhotoLibrary {

    public static function build(): array {
        return [
            // ── Sublimation ───────────────────────────────────────────────
            'jersey'           => self::sequence('img/sublimationPicture/jerseyPicture/jerseyPicture',  1, 16),
            'tshirt'           => self::sequence('img/sublimationPicture/tshirtPicture/tshirtPicture',  1, 25),
            'short'            => self::sequence('img/sublimationPicture/shortPicture/short',           1, 10),
            'warmer'           => self::sequence('img/sublimationPicture/warmerPicture/warmer',         1, 10),
            'joggingpants'     => self::sequence('img/sublimationPicture/pantsPicture/pants',           1,  5),
            'longsleeve'       => self::sequence('img/sublimationPicture/longsleveePicture/longslevee', 1,  6),
            'poloshirt'        => self::sequence('img/sublimationPicture/poloshirtPicture/poloshirt',   1,  9),

            // ── Tarpaulin ─────────────────────────────────────────────────
            'birthday'         => self::sequence('img/tarpaulin/birthdayTarpaulin/bday',               1, 10),
            'congratulation'   => self::manual([
                'img/tarpaulin/congratulationTarpaulin/grats1.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats2.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats3.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats4.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats5.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats6.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats7.jpg',
                // grats8.jpg is missing — skipped intentionally
                'img/tarpaulin/congratulationTarpaulin/grats9.jpg',
                'img/tarpaulin/congratulationTarpaulin/grats10.jpg',
            ]),

            // ── Mugs & Tumbler ────────────────────────────────────────────
            'mug'              => self::sequence('img/sublimationMug/mug/mug',         1, 5),
            'tumbler'          => self::sequence('img/sublimationMug/tumbler/tumbler', 1, 5),

            // ── Lanyard ───────────────────────────────────────────────────
            'lanyard'          => self::sequence('img/idLanyards/lanyard/lanyard',     1, 7),

            // ── Stitching ─────────────────────────────────────────────────
            'stitching-tshirt' => self::sequence('img/customStitching/tshirt/stitching', 1, 5),

            // ── Stickers & Decals ─────────────────────────────────────────
            'motorcycle-decal' => self::sequence('img/sticker_decals/motorDecals/motor', 1, 7),
            'truck-decal'      => self::sequence('img/sticker_decals/truckDecals/truck', 1, 5),
            'car-decal'        => self::sequence('img/sticker_decals/carDecals/car',     1, 5),

            // ── Sintra Board ──────────────────────────────────────────────
            'sintra-board'     => self::sequence('img/sintraBoard/board/board',        1, 5),

            // ── Photo Frame ───────────────────────────────────────────────
            'photo-frame'      => self::sequence('img/photoFrame/frame/frame',         1, 5),

            // ── Ref Magnet ────────────────────────────────────────────────
            'ref-magnet'       => self::sequence('img/refMagnet/magnet/magnet',        1, 5),

            // ── Plaque & Medal ────────────────────────────────────────────
            'plaque'           => self::sequence('img/plaque_medal/plaque/plaque',     1, 10),
            'medal'            => self::sequence('img/plaque_medal/medal/medal',       1, 10),
        ];
    }

    /** Generates a sequential photo array: prefix + N + .jpg */
    private static function sequence(string $prefix, int $from, int $to): array {
        $photos = [];
        for ($i = $from; $i <= $to; $i++) {
            $photos[] = $prefix . $i . '.jpg';
        }
        return $photos;
    }

    /** Returns a manually defined photo array (for irregular numbering). */
    private static function manual(array $paths): array {
        return $paths;
    }
}
?>