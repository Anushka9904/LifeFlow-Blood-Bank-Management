<?php
require_once __DIR__.'/../config/db.php';

class Donor {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $search='', string $blood_group=''): array {
        $sql = "SELECT d.*, u.name, u.email FROM donors d JOIN users u ON d.user_id=u.id WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR d.city LIKE ?)";
            $s = "%$search%"; $params = [$s,$s,$s];
        }
        if ($blood_group) { $sql .= " AND d.blood_group=?"; $params[] = $blood_group; }
        $sql .= " ORDER BY d.created_at DESC";
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT d.*, u.name, u.email FROM donors d JOIN users u ON d.user_id=u.id WHERE d.id=?");
        $stmt->execute([$id]); return $stmt->fetch();
    }

    // Get registered users with role=donor who don't have a donor profile yet
    public function getUnlinkedDonors(): array {
        return $this->db->query("
            SELECT u.id, u.name, u.email, u.created_at
            FROM users u
            WHERE u.role = 'donor'
            AND u.id NOT IN (SELECT user_id FROM donors)
            ORDER BY u.created_at DESC
        ")->fetchAll();
    }

    // Link an existing user account to a donor profile
    public function linkExistingUser(int $user_id, array $data): array {
        // Check user exists and is donor role
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id=? AND role='donor'");
        $stmt->execute([$user_id]);
        if (!$stmt->fetch()) return ['success'=>false,'message'=>'User not found or not a donor.'];

        // Check not already linked
        $stmt2 = $this->db->prepare("SELECT id FROM donors WHERE user_id=?");
        $stmt2->execute([$user_id]);
        if ($stmt2->fetch()) return ['success'=>false,'message'=>'This user already has a donor profile.'];

        $stmt3 = $this->db->prepare("INSERT INTO donors (user_id,blood_group,phone,address,city,dob,gender,weight_kg,is_available) VALUES (?,?,?,?,?,?,?,?,1)");
        $stmt3->execute([
            $user_id,
            $data['blood_group'],
            $data['phone'] ?? '',
            $data['address'] ?? '',
            $data['city'] ?? '',
            !empty($data['dob']) ? $data['dob'] : null,
            !empty($data['gender']) ? $data['gender'] : null,
            !empty($data['weight_kg']) ? $data['weight_kg'] : null,
        ]);
        return ['success'=>true,'message'=>'Donor profile created and linked successfully!'];
    }

    public function create(array $data): array {
        // Check if email already exists as a user
        $stmt = $this->db->prepare("SELECT id, role FROM users WHERE email=?");
        $stmt->execute([trim($data['email'])]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            // User exists — just create donor profile if they are donor role
            if ($existingUser['role'] !== 'donor') {
                return ['success'=>false,'message'=>'This email is registered as '.ucfirst($existingUser['role']).', not a donor.'];
            }
            // Check if donor profile already exists
            $check = $this->db->prepare("SELECT id FROM donors WHERE user_id=?");
            $check->execute([$existingUser['id']]);
            if ($check->fetch()) {
                return ['success'=>false,'message'=>'This email already has a donor profile.'];
            }
            // Link existing user
            return $this->linkExistingUser($existingUser['id'], $data);
        }

        // New user — create user account + donor profile
        $password = !empty($data['password']) ? $data['password'] : 'donor123';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")
            ->execute([trim($data['name']), trim($data['email']), $hash, 'donor']);
        $uid = (int)$this->db->lastInsertId();

        $this->db->prepare("INSERT INTO donors (user_id,blood_group,phone,address,city,dob,gender,weight_kg,is_available) VALUES (?,?,?,?,?,?,?,?,1)")
            ->execute([
                $uid, $data['blood_group'],
                $data['phone'] ?? '', $data['address'] ?? '', $data['city'] ?? '',
                !empty($data['dob']) ? $data['dob'] : null,
                !empty($data['gender']) ? $data['gender'] : null,
                !empty($data['weight_kg']) ? $data['weight_kg'] : null,
            ]);
        return ['success'=>true,'message'=>'Donor account created successfully.'];
    }

    public function update(int $id, array $data): array {
        $stmt = $this->db->prepare("UPDATE donors SET blood_group=?,phone=?,address=?,city=?,dob=?,gender=?,weight_kg=?,is_available=? WHERE id=?");
        $stmt->execute([
            $data['blood_group'], $data['phone']??'', $data['address']??'', $data['city']??'',
            !empty($data['dob']) ? $data['dob'] : null,
            !empty($data['gender']) ? $data['gender'] : null,
            !empty($data['weight_kg']) ? $data['weight_kg'] : null,
            $data['is_available']??1, $id
        ]);
        $stmt2 = $this->db->prepare("UPDATE users SET name=? WHERE id=(SELECT user_id FROM donors WHERE id=?)");
        $stmt2->execute([$data['name'], $id]);
        return ['success'=>true,'message'=>'Donor updated successfully.'];
    }

    public function delete(int $id): array {
        $stmt = $this->db->prepare("SELECT user_id FROM donors WHERE id=?");
        $stmt->execute([$id]); $row = $stmt->fetch();
        if (!$row) return ['success'=>false,'message'=>'Donor not found.'];
        $this->db->prepare("DELETE FROM certificates WHERE donor_id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM donations WHERE donor_id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM donors WHERE id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM users WHERE id=?")->execute([$row['user_id']]);
        return ['success'=>true,'message'=>'Donor deleted.'];
    }

    public function getStats(): array {
        return [
            'total'     => (int)$this->db->query("SELECT COUNT(*) FROM donors")->fetchColumn(),
            'available' => (int)$this->db->query("SELECT COUNT(*) FROM donors WHERE is_available=1")->fetchColumn(),
        ];
    }
}
?>
