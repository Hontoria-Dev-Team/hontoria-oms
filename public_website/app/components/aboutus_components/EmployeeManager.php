<?php
/**
 * EmployeeManager.php
 * Single Responsibility: handles all employee data operations (add, remove, list).
 * Stores data in a JSON file inside Shared/data/employees.json
 */
class EmployeeManager {

    // Path to the employees JSON file
    private string $dataFile;
    private string $uploadDir;

    public function __construct() {
        $base = dirname(dirname(dirname(dirname(__DIR__))));
        $this->dataFile  = $base . '/Shared/data/employees.json';
        $this->uploadDir = $base . '/Shared/img/employees/';

        // Create directories if they don't exist yet
        if (!is_dir(dirname($this->dataFile))) {
            mkdir(dirname($this->dataFile), 0755, true);
        }
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    // ── Get all employees ─────────────────────────────────────────────────
    public function getAll(): array {
        if (!file_exists($this->dataFile)) return [];
        $data = json_decode(file_get_contents($this->dataFile), true);
        return is_array($data) ? $data : [];
    }

    // ── Add a new employee ────────────────────────────────────────────────
    public function add(string $name, string $role, ?array $photoFile = null): bool {
        $employees = $this->getAll();

        // Handle photo upload
        $photoPath = '';
        if ($photoFile && $photoFile['error'] === UPLOAD_ERR_OK) {
            $ext      = pathinfo($photoFile['name'], PATHINFO_EXTENSION);
            $filename = uniqid('emp_') . '.' . strtolower($ext);
            $dest     = $this->uploadDir . $filename;

            if (move_uploaded_file($photoFile['tmp_name'], $dest)) {
                // Store a relative web-accessible path
                $photoPath = '../../Shared/img/employees/' . $filename;
            }
        }

        $employees[] = [
            'name'  => htmlspecialchars(strip_tags($name)),
            'role'  => htmlspecialchars(strip_tags($role)),
            'photo' => $photoPath,
        ];

        return $this->save($employees);
    }

    // ── Remove an employee by name ────────────────────────────────────────
    public function remove(string $name): bool {
        $employees = $this->getAll();

        // Filter out the employee and optionally delete their photo
        $updated = array_filter($employees, function ($emp) use ($name) {
            if ($emp['name'] === $name && !empty($emp['photo'])) {
                $fullPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/' . $emp['photo'];
                if (file_exists($fullPath)) @unlink($fullPath);
            }
            return $emp['name'] !== $name;
        });

        return $this->save(array_values($updated));
    }

    // ── Save the array back to JSON ───────────────────────────────────────
    private function save(array $employees): bool {
        return file_put_contents($this->dataFile, json_encode($employees, JSON_PRETTY_PRINT)) !== false;
    }

    // ── Handle incoming AJAX request ──────────────────────────────────────
    public function handleRequest(): void {
        header('Content-Type: application/json');

        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $name  = trim($_POST['name'] ?? '');
            $role  = trim($_POST['role'] ?? '');
            $photo = $_FILES['photo'] ?? null;

            if (!$name || !$role) {
                echo json_encode(['success' => false, 'message' => 'Name and role are required.']);
                return;
            }

            $ok = $this->add($name, $role, $photo);
            echo json_encode(['success' => $ok]);

        } elseif ($action === 'remove') {
            $name = trim($_POST['name'] ?? '');
            $ok   = $this->remove($name);
            echo json_encode(['success' => $ok]);

        } else {
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        }
    }
}
?>