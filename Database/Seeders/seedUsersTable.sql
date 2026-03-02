INSERT INTO users (id, username, email, passwordHash, firstName, middleName, lastName, phone, isActive, createdAt, lastLoginAt) VALUES
(1, 'owner', 'owner@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Carlos', 'Dela Cruz', '+639171234567', TRUE, '2025-01-01 00:00:00', '2026-02-20 08:30:00'),
(2, 'admin', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Santos', 'Reyes', '+639182345678', TRUE, '2025-01-15 00:00:00', '2026-02-21 09:15:00'),
(3, 'staff', 'staff@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Luis', 'Garcia', '+639193456789', TRUE, '2025-02-01 00:00:00', '2026-02-21 07:45:00');

ALTER TABLE users AUTO_INCREMENT = 4;
