<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * EmployeesSectionComponent.php
 * Single Responsibility: renders the Meet Our People / Employees section only.
 */
class EmployeesSectionComponent extends \Component {

    public function render(): string {
        $employees = $this->get('employees', []);

        ob_start();
        ?>
        <section class="section employees-section" id="employees">
            <div class="section-inner">

                <div class="employees-header">
                    <div>
                        <p class="section-eyebrow">The Team</p>
                        <h2 class="section-title" id="employeesTitle">Meet Our People</h2>
                        <div class="section-line"></div>
                    </div>
                    <button class="btn-add-employee" id="btnAddEmployee">
                        <i class="fas fa-plus"></i> Add Employee
                    </button>
                </div>

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

                        <button class="btn-remove-employee" title="Remove employee">
                            <i class="fas fa-times"></i>
                        </button>

                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>

        <!-- Add Employee Modal -->
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
}
?>