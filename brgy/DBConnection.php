<?php
date_default_timezone_set('Asia/Manila');

// ── MySQL Database Configuration ──
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barangay_management_db');

/**
 * Thin wrapper around a mysqli_result so existing pages that were written
 * against a SQLite-style API ($qry->fetchArray()) keep working, while
 * code that already uses mysqli's own fetch_array()/fetch_assoc() also
 * keeps working unchanged.
 */
class DBResult {
    private $result;

    function __construct($result) {
        $this->result = $result;
    }

    // SQLite-style alias used by most of the admin pages
    function fetchArray() {
        if (!$this->result || !($this->result instanceof mysqli_result)) return false;
        $row = $this->result->fetch_assoc();
        return $row === null ? false : $row;
    }

    // mysqli-style methods, used by Actions.php
    function fetch_array($mode = MYSQLI_BOTH) {
        if (!$this->result || !($this->result instanceof mysqli_result)) return false;
        $row = $this->result->fetch_array($mode);
        return $row === null ? false : $row;
    }

    function fetch_assoc() {
        if (!$this->result || !($this->result instanceof mysqli_result)) return false;
        $row = $this->result->fetch_assoc();
        return $row === null ? false : $row;
    }

    function num_rows() {
        return ($this->result instanceof mysqli_result) ? $this->result->num_rows : 0;
    }

    // Allow $qry->num_rows access as a property too
    function __get($name) {
        if ($name === 'num_rows') return $this->num_rows();
        return null;
    }
}

class DBConnection {
    protected $db;

    function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }

        $this->db->set_charset("utf8");

        // Load system info into session
        $information = $this->db->query("SELECT * FROM `system_info`");
        if ($information) {
            while ($row = $information->fetch_assoc()) {
                $_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
            }
        }
    }

    // Run a query and return a DBResult wrapper (for SELECT),
    // or the raw boolean/mysqli result for INSERT/UPDATE/DELETE.
    function query($sql) {
        $result = $this->db->query($sql);
        if ($result instanceof mysqli_result) {
            return new DBResult($result);
        }
        return $result; // true/false for write queries
    }

    // Escape string to prevent SQL injection
    function escape($str) {
        return $this->db->real_escape_string($str);
    }

    // Alias kept for backwards compatibility with code that calls escapeString()
    function escapeString($str) {
        return $this->escape($str);
    }

    // Returns the most recent MySQL error message, if any
    function lastErrorMsg() {
        return $this->db->error;
    }

    // Get last inserted ID
    function lastInsertId() {
        return $this->db->insert_id;
    }

    // Get number of affected rows
    function affectedRows() {
        return $this->db->affected_rows;
    }

    // Check if mobile device
    function isMobileDevice() {
        $aMobileUA = array(
            '/iphone/i'     => 'iPhone',
            '/ipod/i'       => 'iPod',
            '/ipad/i'       => 'iPad',
            '/android/i'    => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i'      => 'Mobile'
        );

        foreach ($aMobileUA as $sMobileKey => $sMobileOS) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }
        }
        return false;
    }

    function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}
