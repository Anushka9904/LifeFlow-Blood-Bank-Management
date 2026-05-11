<?php
require_once __DIR__.'/../config/db.php';

class BloodRequest {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $status=''): array {
        $sql = "SELECT br.*, u.name as hospital_name FROM blood_requests br JOIN users u ON br.hospital_id=u.id WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND br.status=?"; $params[] = $status; }
        $sql .= " ORDER BY br.requested_at DESC";
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByHospital(int $hospital_id, string $status=''): array {
        $sql = "SELECT * FROM blood_requests WHERE hospital_id=?";
        $params = [$hospital_id];
        if ($status) { $sql .= " AND status=?"; $params[] = $status; }
        $sql .= " ORDER BY requested_at DESC";
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data, int $hospital_id): array {
        $stmt = $this->db->prepare("INSERT INTO blood_requests (hospital_id,patient_name,blood_group,units_needed,urgency,notes) VALUES (?,?,?,?,?,?)");
        $stmt->execute([
            $hospital_id,
            $data['patient_name'] ?? '',
            $data['blood_group'],
            (int)$data['units_needed'],
            $data['urgency'] ?? 'normal',
            $data['notes'] ?? ''
        ]);
        return ['success'=>true,'message'=>'Blood request submitted.'];
    }

    public function updateStatus(int $id, string $status): array {
        $fulfilled = ($status==='fulfilled') ? date('Y-m-d H:i:s') : null;
        $this->db->prepare("UPDATE blood_requests SET status=?, fulfilled_at=? WHERE id=?")->execute([$status,$fulfilled,$id]);
        return ['success'=>true,'message'=>'Status updated.'];
    }

    public function delete(int $id): array {
        $this->db->prepare("DELETE FROM blood_requests WHERE id=?")->execute([$id]);
        return ['success'=>true,'message'=>'Request deleted.'];
    }

    public function getPendingCount(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM blood_requests WHERE status='pending'")->fetchColumn();
    }
}
?>
