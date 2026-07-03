<?php
include_once(__DIR__ . '/../vendor/autoload.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

class DB
{
    private $connect;
    public $last_sql = '';
    public $last_error = '';

    function connect()
    {
        if (!$this->connect) {
            try {
                $dsn = "pgsql:host=" . SUPABASE_DB_HOST . ";port=" . SUPABASE_DB_PORT . ";dbname=" . SUPABASE_DB_NAME;
                $this->connect = new PDO($dsn, SUPABASE_DB_USER, SUPABASE_DB_PASSWORD);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->exec("SET names 'utf8'");
            } catch (PDOException $e) {
                $this->last_error = $e->getMessage();
                die('Error => DATABASE: ' . $e->getMessage());
            }
        }
    }

    private function prepare_query($sql)
    {
        // Remove backticks for PostgreSQL compatibility
        $sql = str_replace('`', '', $sql);
        // Replace RAND() with RANDOM()
        $sql = preg_replace('/\bRAND\s*\(\s*\)/i', 'RANDOM()', $sql);
        return $sql;
    }

    public function escape($value)
    {
        $this->connect();
        if ($value === null) {
            return 'NULL';
        }
        $quoted = $this->connect->quote($value);
        // Remove leading and trailing quotes added by quote()
        return substr($quoted, 1, -1);
    }

    public function get_id_insert()
    {
        $this->connect();
        return $this->connect->lastInsertId();
    }

    public function dis_connect()
    {
        $this->connect = null;
    }

    public function query($sql)
    {
        $this->connect();
        $sql = $this->prepare_query($sql);
        $this->last_sql = $sql;
        try {
            return $this->connect->query($sql);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    public function insert($table, $data)
    {
        $this->connect();
        $table = $this->prepare_query($table);
        
        $fields = array();
        $values = array();
        foreach ($data as $key => $value) {
            $fields[] = $key;
            if ($value === null) {
                $values[] = 'NULL';
            } else {
                $values[] = $this->connect->quote($value);
            }
        }

        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
        $this->last_sql = $sql;

        try {
            $result = $this->connect->exec($sql);
            return $result !== false;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    public function update($table, $data, $where)
    {
        $this->connect();
        $table = $this->prepare_query($table);
        $where = $this->prepare_query($where);

        $sets = array();
        foreach ($data as $key => $value) {
            if ($value === null) {
                $sets[] = "$key = NULL";
            } else {
                $sets[] = "$key = " . $this->connect->quote($value);
            }
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $sets) . ' WHERE ' . $where;
        $this->last_sql = $sql;

        try {
            $result = $this->connect->exec($sql);
            return $result !== false;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    public function update_value($table, $data, $where, $value1)
    {
        $this->connect();
        $table = $this->prepare_query($table);
        $where = $this->prepare_query($where);

        $sets = array();
        foreach ($data as $key => $value) {
            $sets[] = "$key = " . $this->connect->quote($value);
        }
        // PostgreSQL does not support LIMIT in direct UPDATE. We drop it or rewrite it.
        // Since update_value is unused, we just format it simply.
        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $sets) . ' WHERE ' . $where;
        $this->last_sql = $sql;

        try {
            $result = $this->connect->exec($sql);
            return $result !== false;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    function cong($table, $data, $sotien, $where)
    {
        $this->connect();
        $table = $this->prepare_query($table);
        $data = $this->prepare_query($data);
        $where = $this->prepare_query($where);
        
        $sql = "UPDATE $table SET $data = $data + " . floatval($sotien) . " WHERE $where";
        $this->last_sql = $sql;
        try {
            return $this->connect->query($sql);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    function tru($table, $data, $sotien, $where)
    {
        $this->connect();
        $table = $this->prepare_query($table);
        $data = $this->prepare_query($data);
        $where = $this->prepare_query($where);

        $sql = "UPDATE $table SET $data = $data - " . floatval($sotien) . " WHERE $where";
        $this->last_sql = $sql;
        try {
            return $this->connect->query($sql);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    function getUser($username)
    {
        $this->connect();
        try {
            $stmt = $this->connect->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    public function site($data)
    {
        $this->connect();
        try {
            $stmt = $this->connect->prepare("SELECT value FROM options WHERE key = :key");
            $stmt->execute([':key' => $data]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['value'] : null;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return null;
        }
    }

    public function remove($table, $where)
    {
        $this->connect();
        $table = $this->prepare_query($table);
        $where = $this->prepare_query($where);

        $sql = "DELETE FROM $table WHERE $where";
        $this->last_sql = $sql;
        try {
            return $this->connect->exec($sql) !== false;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    public function get_list($sql)
    {
        $this->connect();
        $sql = $this->prepare_query($sql);
        $this->last_sql = $sql;
        try {
            $stmt = $this->connect->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            die('Câu truy vấn bị sai: ' . $e->getMessage());
        }
    }

    public function get_row($sql)
    {
        $this->connect();
        $sql = $this->prepare_query($sql);
        $this->last_sql = $sql;
        try {
            $stmt = $this->connect->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            die('Câu truy vấn bị sai: ' . $e->getMessage());
        }
    }

    public function num_rows($sql)
    {
        $this->connect();
        $sql = $this->prepare_query($sql);
        $this->last_sql = $sql;
        try {
            $stmt = $this->connect->query($sql);
            // Fetch all is the safest way to count rows in Postgres SELECT queries
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return count($rows);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            die('Câu truy vấn bị sai: ' . $e->getMessage());
        }
    }
}
