<?php
require_once __DIR__ . '/../home_components/Component.php';

/**
 * AboutUsComponent.php
 * Renders the full About Us page content:
 * history, workplace areas, products, location, owner, and employees.
 */
class AboutUsComponent extends \Component {

    public function render(): string {

        // ── Load employees from JSON file (our simple "database") ────────
        $employees = $this->loadEmployees();

        ob_start();
        ?>

        <!-- ══ PAGE HERO ══════════════════════════════════════════════════ -->
        <div class="page-hero">
            <p class="page-hero-eyebrow">Who We Are</p>
            <h1 class="page-hero-title">About <span>Hontoria</span></h1>
            <p class="page-hero-sub">A family-built printing business rooted in quality, creativity, and community.</p>
        </div>

        <!-- ══ HISTORY ════════════════════════════════════════════════════ -->
        <section class="section history-section">
            <div class="section-inner">
                <div class="history-layout">

                    <!-- Text side -->
                    <div class="history-text">
                        <p class="section-eyebrow">Our Story</p>
                        <h2 class="section-title">How It All Started</h2>
                        <div class="section-line"></div>
                        <p>Hontoria Printing Services was founded with a simple mission: deliver high-quality, affordable printed products to the community of Davao del Norte. What started as a small home-based printing setup has grown into a full-service print shop trusted by hundreds of customers.</p>
                        <p>From humble beginnings with a single heat press machine, we have expanded our services to include sublimation printing, tarpaulin printing, and custom tailoring — all under one roof.</p>
                        <p>Every product that leaves our shop carries the same commitment to quality and craftsmanship that our founder built this business on.</p>
                    </div>

                    <!-- Image side -->
                    <div class="history-img-wrap">
                        <div class="history-img-placeholder">
                            <i class="fas fa-store"></i>
                            <span>SHOP PHOTO</span>
                        </div>
                        <div class="history-badge">
                            <span>Est.</span>
                            2018
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ══ WORKPLACE AREAS ════════════════════════════════════════════ -->
        <section class="section workplace-section">
            <div class="section-inner">
                <p class="section-eyebrow">Our Workspace</p>
                <h2 class="section-title">Inside the Shop</h2>
                <div class="section-line"></div>
                <p class="section-desc" style="margin-bottom:36px">Our shop is organized into dedicated areas so every job gets the focused attention it deserves.</p>

                <div class="areas-grid">

                    <!-- Layout Area -->
                    <div class="area-card">
                        <div class="area-img-placeholder area-layout">
                            <i class="fas fa-desktop"></i>
                            <span>LAYOUT AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Layout Area</div>
                            <div class="area-desc">Where our designers bring your ideas to life using professional design software. Every print starts here.</div>
                        </div>
                    </div>

                    <!-- Printing Area -->
                    <div class="area-card">
                        <div class="area-img-placeholder area-print">
                            <i class="fas fa-print"></i>
                            <span>PRINTING AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Printing Area</div>
                            <div class="area-desc">Equipped with high-quality printers capable of producing vivid, sharp, and durable prints on multiple media.</div>
                        </div>
                    </div>

                    <!-- Tailor Area -->
                    <div class="area-card">
                        <div class="area-img-placeholder area-tailor">
                            <i class="fas fa-cut"></i>
                            <span>TAILOR AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Tailor Area</div>
                            <div class="area-desc">Our skilled tailors handle custom garments and alterations with precision and care for every stitch.</div>
                        </div>
                    </div>

                    <!-- Heatpress Area -->
                    <div class="area-card">
                        <div class="area-img-placeholder area-heat">
                            <i class="fas fa-fire"></i>
                            <span>HEATPRESS AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Heatpress Area</div>
                            <div class="area-desc">Sublimation and heat transfer printing station where designs are permanently bonded onto fabric and hard surfaces.</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ══ PRODUCTS ═══════════════════════════════════════════════════ -->
        <section class="section products-section">
            <div class="section-inner">
                <p class="section-eyebrow">What We Offer</p>
                <h2 class="section-title">Our Products</h2>
                <div class="section-line"></div>
                <p class="section-desc" style="margin-bottom:36px">Quality products at prices that won't break the bank.</p>

                <div class="products-grid">
                    <div class="product-pill"><i class="fas fa-tshirt"></i><span>Sublimation Shirts</span></div>
                    <div class="product-pill"><i class="fas fa-user-tie"></i><span>Jersey / Uniform</span></div>
                    <div class="product-pill"><i class="fas fa-scroll"></i><span>Tarpaulin Banners</span></div>
                    <div class="product-pill"><i class="fas fa-mug-hot"></i><span>Sublimation Mugs</span></div>
                    <div class="product-pill"><i class="fas fa-id-card"></i><span>ID Lanyards</span></div>
                    <div class="product-pill"><i class="fas fa-ruler-combined"></i><span>Custom Stitching</span></div>
                    <div class="product-pill"><i class="fas fa-image"></i><span>Photo Prints</span></div>
                    <div class="product-pill"><i class="fas fa-tags"></i><span>Stickers & Labels</span></div>
                </div>
            </div>
        </section>

        <!-- ══ LOCATION ════════════════════════════════════════════════════ -->
        <section class="section location-section">
            <div class="section-inner">
                <div class="location-layout">

                    <!-- Map / Photo placeholder -->
                    <div class="location-img-placeholder">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>LOCATION PHOTO</span>
                    </div>

                    <!-- Details -->
                    <div class="location-details">
                        <p class="section-eyebrow">Find Us</p>
                        <h2 class="section-title">Our Location</h2>
                        <div class="section-line"></div>

                        <div class="location-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="location-item-text">
                                <strong>Address</strong>
                                <span>Feeder Road 2, Brgy. Tibal-og<br>Santo Tomas, Davao del Norte</span>
                            </div>
                        </div>

                        <div class="location-item">
                            <i class="fab fa-facebook"></i>
                            <div class="location-item-text">
                                <strong>Facebook Page</strong>
                                <span><a href="https://www.facebook.com/jhong.hontoria.3" target="_blank" style="color:var(--red)">Hontoria Printing Services</a></span>
                            </div>
                        </div>

                        <div class="location-item">
                            <i class="fas fa-clock"></i>
                            <div class="location-item-text">
                                <strong>Business Hours</strong>
                                <span>Monday – Saturday: 8:00 AM – 6:00 PM</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- ══ OWNER ════════════════════════════════════════════════════════ -->
        <section class="section owner-section">
            <div class="section-inner">
                <div class="owner-layout">

                    <!-- Owner photo -->
                    <div class="owner-img-wrap">
                        <div class="owner-img-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="owner-tag">Owner</span>
                    </div>

                    <!-- Owner info -->
                    <div class="owner-info">
                        <p class="section-eyebrow">Meet the Owner</p>
                        <div class="owner-name">Jhong Hontoria</div>
                        <div class="owner-title">Founder &amp; Owner</div>
                        <p class="owner-bio">Jhong built Hontoria Printing Services from the ground up with a passion for quality craftsmanship and a drive to serve the local community. With years of hands-on experience in printing and garment production, he leads the team with dedication and an eye for detail.</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- ══ EMPLOYEES ════════════════════════════════════════════════════ -->
        <section class="section employees-section">
            <div class="section-inner">

                <div class="employees-header">
                    <div>
                        <p class="section-eyebrow">The Team</p>
                        <h2 class="section-title" id="employeesTitle">Meet Our People</h2>
                        <div class="section-line"></div>
                    </div>
                    <!-- Add Employee button (visible to admin — hide via CSS if needed) -->
                    <button class="btn-add-employee" id="btnAddEmployee">
                        <i class="fas fa-plus"></i> Add Employee
                    </button>
                </div>

                <!-- Employee cards rendered from PHP -->
                <div class="employees-grid" id="employeesGrid">
                    <?php foreach ($employees as $emp): ?>
                    <div class="employee-card" data-name="<?php echo htmlspecialchars($emp['name']); ?>">

                        <?php if (!empty($emp['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($emp['photo']); ?>"
                                 alt="<?php echo htmlspecialchars($emp['name']); ?>"
                                 class="employee-img"/>
                        <?php else: ?>
                            <div class="employee-img-placeholder">
                                <i class="fas fa-user"></i>
                                <span>NO PHOTO</span>
                            </div>
                        <?php endif; ?>

                        <div class="employee-info">
                            <div class="employee-name"><?php echo htmlspecialchars($emp['name']); ?></div>
                            <div class="employee-role"><?php echo htmlspecialchars($emp['role']); ?></div>
                        </div>

                        <!-- Remove button shown only in admin mode -->
                        <button class="btn-remove-employee" title="Remove employee">
                            <i class="fas fa-times"></i>
                        </button>

                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>

        <!-- ══ ADD EMPLOYEE MODAL ══════════════════════════════════════════ -->
        <div class="modal-overlay" id="addEmployeeModal">
            <div class="modal">
                <button class="modal-close" id="modalClose"><i class="fas fa-times"></i></button>
                <div class="modal-title">Add New Employee</div>

                <form id="addEmployeeForm" enctype="multipart/form-data">

                    <div class="form-group">
                        <label class="form-label" for="empName">Full Name</label>
                        <input class="form-input" type="text" id="empName" placeholder="e.g. Juan Dela Cruz" required/>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="empRole">Role / Position</label>
                        <select class="form-select" id="empRole" required>
                            <option value="">— Select role —</option>
                            <option value="Layout Artist">Layout Artist</option>
                            <option value="Printing">Printing</option>
                            <option value="Heatpress">Heatpress</option>
                            <option value="Tailor">Tailor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Photo (optional)</label>
                        <label class="form-file-label" for="empPhoto">
                            <i class="fas fa-camera"></i>
                            <span>Click to upload photo</span>
                        </label>
                        <input class="form-file-input" type="file" id="empPhoto" accept="image/*"/>
                        <img class="form-preview" id="photoPreview" alt="Preview"/>
                    </div>

                    <button class="btn-submit" type="submit">
                        <i class="fas fa-plus"></i> Add Employee
                    </button>

                </form>
            </div>
        </div>

        <?php
        return ob_get_clean();
    }

    // ── Load employees from JSON file ─────────────────────────────────────
    private function loadEmployees(): array {
        $file = __DIR__ . '/../../../../Shared/data/employees.json';
        if (!file_exists($file)) return $this->getDefaultEmployees();
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : $this->getDefaultEmployees();
    }

    // ── Default employees shown when no JSON file exists yet ──────────────
    private function getDefaultEmployees(): array {
        return [
            ['name' => 'Juan Dela Cruz',  'role' => 'Layout Artist', 'photo' => ''],
            ['name' => 'Maria Santos',    'role' => 'Printing',      'photo' => ''],
            ['name' => 'Pedro Reyes',     'role' => 'Heatpress',     'photo' => ''],
            ['name' => 'Ana Villanueva',  'role' => 'Tailor',        'photo' => ''],
        ];
    }
}
?>