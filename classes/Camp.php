<?php
require_once __DIR__.'/../config/db.php';

class Camp {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $status=''): array {
        $sql = "SELECT * FROM donation_camps WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND status=?"; $params[] = $status; }
        $sql .= " ORDER BY camp_date ASC";
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): array {
        $stmt = $this->db->prepare("INSERT INTO donation_camps (name,location,city,camp_date,start_time,end_time,organizer,contact,max_capacity) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['name'],
            $data['location'] ?? '',
            $data['city'] ?? '',
            $data['camp_date'],
            !empty($data['start_time']) ? $data['start_time'] : null,
            !empty($data['end_time']) ? $data['end_time'] : null,
            $data['organizer'] ?? '',
            $data['contact'] ?? '',
            (int)($data['max_capacity'] ?? 100)
        ]);
        return ['success'=>true,'message'=>'Camp scheduled successfully.'];
    }

    public function update(int $id, array $data): array {
        $stmt = $this->db->prepare("UPDATE donation_camps SET name=?,location=?,city=?,camp_date=?,start_time=?,end_time=?,organizer=?,contact=?,max_capacity=?,status=? WHERE id=?");
        $stmt->execute([
            $data['name'], $data['location']??'', $data['city']??'',
            $data['camp_date'],
            !empty($data['start_time']) ? $data['start_time'] : null,
            !empty($data['end_time']) ? $data['end_time'] : null,
            $data['organizer']??'', $data['contact']??'',
            (int)($data['max_capacity']??100),
            $data['status']??'upcoming', $id
        ]);
        return ['success'=>true,'message'=>'Camp updated.'];
    }

    public function delete(int $id): array {
        $this->db->prepare("DELETE FROM donation_camps WHERE id=?")->execute([$id]);
        return ['success'=>true,'message'=>'Camp deleted.'];
    }

    public function getUpcomingCount(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM donation_camps WHERE status='upcoming' AND camp_date>=CURDATE()")->fetchColumn();
    }
}
?>
