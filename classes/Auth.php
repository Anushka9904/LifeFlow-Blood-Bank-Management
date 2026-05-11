<?php
require_once __DIR__.'/../config/db.php';

class Auth {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function register(array $data): array {
        $name     = trim($data['name'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role     = trim($data['role'] ?? 'donor');

        // Validate role strictly
        if (!in_array($role, ['admin','donor','hospital'])) $role = 'donor';

        if (empty($name)||empty($email)||empty($password))
            return ['success'=>false,'message'=>'All fields are required.'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return ['success'=>false,'message'=>'Invalid email address.'];
        if (strlen($password) < 6)
            return ['success'=>false,'message'=>'Password must be at least 6 characters.'];

        $stmt = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) return ['success'=>false,'message'=>'Email already registered.'];

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
        $stmt->execute([$name,$email,$hash,$role]);

        return ['success'=>true,'message'=>'Registration successful! You registered as: '.ucfirst($role).'. Please log in.'];
    }

    public function login(string $email, string $password): array {
        $email = trim($email);
        if (empty($email)||empty($password))
            return ['success'=>false,'message'=>'Email and password are required.'];
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=? AND is_active=1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password']))
            return ['success'=>false,'message'=>'Invalid email or password.'];
        if (session_status()===PHP_SESSION_NONE) session_start();
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $redirect = match($user['role']) {
            'admin'    => '/bloodbank/pages/dashboard.php',
            'hospital' => '/bloodbank/pages/hospital_portal.php',
            'donor'    => '/bloodbank/pages/donor_portal.php',
            default    => '/bloodbank/login.php'
        };
        return ['success'=>true,'message'=>'Login successful.','role'=>$user['role'],'redirect'=>$redirect];
    }

    public function logout(): void {
        if (session_status()===PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$p["path"],$p["domain"],$p["secure"],$p["httponly"]);
        }
        session_destroy();
        header('Location: /bloodbank/login.php');
        exit;
    }

    public static function requireLogin(): void {
        if (session_status()===PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['logged_in'])) {
            header('Location: /bloodbank/login.php'); exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if (($_SESSION['user_role']??'') !== 'admin') {
            header('Location: /bloodbank/index.php'); exit;
        }
    }

    public static function requireHospital(): void {
        self::requireLogin();
        if (!in_array($_SESSION['user_role']??'', ['admin','hospital'])) {
            header('Location: /bloodbank/index.php'); exit;
        }
    }

    public static function requireDonor(): void {
        self::requireLogin();
        if (!in_array($_SESSION['user_role']??'', ['admin','donor'])) {
            header('Location: /bloodbank/index.php'); exit;
        }
    }

    public static function currentUser(): array {
        if (session_status()===PHP_SESSION_NONE) session_start();
        return [
            'id'   => $_SESSION['user_id']   ?? null,
            'name' => $_SESSION['user_name'] ?? 'Guest',
            'role' => $_SESSION['user_role'] ?? '',
        ];
    }

    public static function isLoggedIn(): bool {
        if (session_status()===PHP_SESSION_NONE) session_start();
        return !empty($_SESSION['logged_in']);
    }
}
?>
