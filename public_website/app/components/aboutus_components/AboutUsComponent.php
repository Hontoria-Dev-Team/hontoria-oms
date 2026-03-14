<?php
require_once __DIR__ . '/../reusable_components/Component.php';
require_once __DIR__ . '/HeroSectionComponent.php';
require_once __DIR__ . '/AboutUsSidebarComponent.php';
require_once __DIR__ . '/HistorySectionComponent.php';
require_once __DIR__ . '/WorkplaceSectionComponent.php';
require_once __DIR__ . '/ProductsSectionComponent.php';
require_once __DIR__ . '/LocationSectionComponent.php';
require_once __DIR__ . '/OwnerSectionComponent.php';
require_once __DIR__ . '/EmployeesSectionComponent.php';

/**
 * AboutUsComponent.php
 * Orchestrator: assembles all About Us sub-components.
 * Single Responsibility: layout only — content is delegated to each sub-component.
 *
 * Sub-components:
 *   HeroSectionComponent       — page hero banner
 *   AboutUsSidebarComponent    — sticky section navigation sidebar
 *   HistorySectionComponent    — Our Story
 *   WorkplaceSectionComponent  — Inside the Shop
 *   ProductsSectionComponent   — Our Products
 *   LocationSectionComponent   — Our Location
 *   OwnerSectionComponent      — Meet the Owner
 *   EmployeesSectionComponent  — Meet Our People + Add Employee modal
 */
class AboutUsComponent extends \Component {

    public function render(): string {
        $fbLink  = $this->get('fbLink',  '');
        $address = $this->get('address', '');

        // ── Load employees from JSON ──────────────────────────────────────
        $employees = $this->loadEmployees();

        // ── Render each sub-component ─────────────────────────────────────
        $hero      = (new \HeroSectionComponent([]))->render();
        $sidebar   = (new \AboutUsSidebarComponent([]))->render();
        $history   = (new \HistorySectionComponent([]))->render();
        $workplace = (new \WorkplaceSectionComponent([]))->render();
        $products  = (new \ProductsSectionComponent([]))->render();
        $location  = (new \LocationSectionComponent(['fbLink' => $fbLink, 'address' => $address]))->render();
        $owner     = (new \OwnerSectionComponent([]))->render();
        $employees = (new \EmployeesSectionComponent(['employees' => $employees]))->render();

        ob_start();
        ?>
        <!-- Hero — full width above the sidebar layout -->
        <?php echo $hero; ?>

        <!-- Body: sidebar + main content -->
        <div class="about-body">

            <!-- Sticky sidebar navigation -->
            <?php echo $sidebar; ?>

            <!-- Main content: all sections -->
            <main class="about-content">
                <?php echo $history; ?>
                <?php echo $workplace; ?>
                <?php echo $products; ?>
                <?php echo $location; ?>
                <?php echo $owner; ?>
                <?php echo $employees; ?>
            </main>

        </div>
        <?php
        return ob_get_clean();
    }

    // ── Load employees from JSON ──────────────────────────────────────────
    private function loadEmployees(): array {
        $file = __DIR__ . '/../../../../Shared/data/employees.json';
        if (!file_exists($file)) return $this->getDefaultEmployees();
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : $this->getDefaultEmployees();
    }

    private function getDefaultEmployees(): array {
        return [
            ['name' => 'Juan Dela Cruz', 'role' => 'Layout Artist', 'photo' => ''],
            ['name' => 'Maria Santos',   'role' => 'Printing',      'photo' => ''],
            ['name' => 'Pedro Reyes',    'role' => 'Heatpress',     'photo' => ''],
            ['name' => 'Ana Villanueva', 'role' => 'Tailor',        'photo' => ''],
        ];
    }
}
?>