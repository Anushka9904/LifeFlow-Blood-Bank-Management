<?php
require_once __DIR__.'/../config/db.php';

class BloodInventory {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM blood_inventory ORDER BY FIELD(blood_group,'O+','O-','A+','A-','B+','B-','AB+','AB-')")->fetchAll();
    }

    public function addUnits(string $bg, int $units): array {
        if ($units <= 0) return ['success'=>false,'message'=>'Units must be greater than 0.'];
        $this->db->prepare("UPDATE blood_inventory SET units=units+? WHERE blood_group=?")->execute([$units,$bg]);
        return ['success'=>true,'message'=>"Added $units units to $bg."];
    }

    public function deductUnits(string $bg, int $units): array {
        if ($units <= 0) return ['success'=>false,'message'=>'Units must be greater than 0.'];
        // Safe parameterized query to get current units
        $stmt = $this->db->prepare("SELECT units FROM blood_inventory WHERE blood_group=?");
        $stmt->execute([$bg]);
        $cur = (int)$stmt->fetchColumn();
        if ($cur < $units) return ['success'=>false,'message'=>"Not enough stock. Only $cur unit(s) available for $bg."];
        $this->db->prepare("UPDATE blood_inventory SET units=units-? WHERE blood_group=?")->execute([$units,$bg]);
        return ['success'=>true,'message'=>"Deducted $units units from $bg."];
    }

    public function setUnits(string $bg, int $units): array {
        if ($units < 0) return ['success'=>false,'message'=>'Units cannot be negative.'];
        $this->db->prepare("UPDATE blood_inventory SET units=? WHERE blood_group=?")->execute([$units,$bg]);
        return ['success'=>true,'message'=>"$bg set to $units units."];
    }

    public function getCritical(): array {
        return $this->db->query("SELECT * FROM blood_inventory WHERE units<=critical_level")->fetchAll();
    }

    public function getTotalUnits(): int {
        return (int)$this->db->query("SELECT COALESCE(SUM(units),0) FROM blood_inventory")->fetchColumn();
    }
}
?>
