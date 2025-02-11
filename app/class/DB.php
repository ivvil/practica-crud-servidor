<?php

namespace App\Crud;

use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;

class DB
{
    private static ?DB $instance = null;
    public mysqli $con;

    private function __construct()
    {
        $host = "mysql";
        $user = "alumno";
        $pass = "alumno";
        $db = "tienda";
            /* $ROOT_PASSWORD="root_password"   */;

        /* $host = $_ENV['HOST'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['PASSWORD'];
        $db = $_ENV['DATABASE']; */


        try {
            $this->con = new mysqli($host, $user, $pass, $db);
            if ($this->con->connect_error) {
                throw new DBError("Connection failed: " . $this->con->connect_error);
            }
        } catch (mysqli_sql_exception $e) {
            throw new DBError("Database error: " . $e->getMessage());
        }
    }

    public static function getInstance(): DB
    {
        if (self::$instance === null) {
            self::$instance = new DB();
        }

        return self::$instance;
    }

    /*
     * Este método tendría que investigar en el diccionario de datos
     * Devolverá qué campos de esta tabla son claves foráneas
     * */
    /**
     * @throws DBError
     */
    public function get_foraneas(string $tabla): array // Ni idea si esto va a funcionar
    {
        $res = $this->exec_stmt("
        SELECT
            CONSTRAINT_NAME,
            TABLE_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = ? AND
            TABLE_NAME = ? AND
            REFERENCED_TABLE_NAME IS NOT NULL;
       ", 'ss', ['tienda', $tabla]);

        return $res;
    }

    public function get_schema(string $tabla): array
    {
        $res = $this->exec_stmt("
        SELECT
            COLUMN_NAME,
            DATA_TYPE,
            EXTRA
        FROM
            INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = ? AND
            TABLE_NAME = ?;     
        ", 'ss', ['tienda', $tabla]);

        return $res;
    }


    public function get_tablas(): array
    {
        $res = $this->exec_stmt('SHOW TABLES', "", []);
        if (!$res) {
            throw new DBError('Prepare failed: ' . $this->con->error);
        }

        $tablas = $this->arrayFlatten($res);

        return $tablas;
    }

    // Retorna un array con las filas de una tabla
    public function get_filas(string $tabla): array
    {
        $res = $this->exec_stmt("SELECT * FROM " . $this->con->real_escape_string($tabla), "", []);

        if (!$res) {
            throw new DBError('Prepare failed: ' . $this->con->error);
        }

        return $res;
    }

    //Borra una fila de una tabla dada su código
    //Retorna un mensaje diciendo si lo ha podido borrar o no
    public function borrar_fila(string $table, mixed $cod): bool
    {

        $tipo_cod = match (gettype($cod)) {
            "string" => "s",
            "integer" => "d"
        };
        $res = $this->exec_stmt("DELETE FROM " . $this->con->real_escape_string($table) . " WHERE cod = ?", $tipo_cod, [$cod]);
        if (!$res[0]) {
            throw new DBError('Delete failed: ' . $this->con->error);
        }
        if (!$res[0]) {
            return false;
        }

        return true;
    }

    public function close()
    {
        $this->con->close();
    }

    /* 
     * Añade una fila cuyos valores se pasan en un array.
     * Tengo el nombre de la tabla y el array ["nombre_Campo"=>"valor"]
    */
    public function add_fila(string $tabla, array $campos): bool
    {

        $field_types = "";
        $field_names = [];
        $field_values = [];
        $table_schema = $this->get_schema($tabla);
        
        foreach ($table_schema as $column) {
            $field_names[] = $column['COLUMN_NAME'];
            $field_types .= match ($column['DATA_TYPE']) {
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint' => 'i',
            'float', 'double', 'decimal' => 'd',
            default => 's',
            };
            $field_values[] = $campos[$column['COLUMN_NAME']] ?? null;
        }

        $placeholders = implode(',', array_fill(0, count($field_names), '?'));
        $field_names_str = implode(',', $field_names);

        $res = $this->exec_stmt(
            "INSERT INTO " . $this->con->real_escape_string($tabla) . " (".$this->con->real_escape_string($field_names_str).") VALUES ($placeholders)",
            $field_types,
            $field_values
        );

        if (!$res[0]) {
            return false;
        }

        return true;
    }

    //Registra un usuario en la tabla usuarios y me pasan el nombre y el pass
    //El pass tiene que estar cifrado antes de insertar
    //Retorna un bool = true si ha ido bien o un mensaje si ha ocurrdio algún problema, como que el usuario ya existiese
    public function registrar_usuario(string $nombre, string $pass): void
    {
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        $res = $this->exec_stmt('INSERT INTO usuarios (nombre, password) VALUES (?, ?)', "ss", [$nombre, $pass_hash]);
        if (!$res[0]) {
            throw new DBError('Insert Failed');
        }
    }

    public function existe_usuario(string $nombre): bool
    {
        $res = $this->exec_stmt("SELECT * FROM usuarios WHERE nombre = ?", "s", [$nombre]);
        if (empty($res)) {
            return false;
        }

        return count($res) === 1;
    }

    public function comprobar_contrasegna(string $nombre, string $pass): bool
    {
        $res = $this->exec_stmt("SELECT password FROM usuarios WHERE nombre = ?", "s", [$nombre]);
        if (!empty($res)) {
            if (count($res) > 1) {
                throw new DBError("QUE COÑO MAS DE 1 USUARIO");
            }
            $hashedPass = $res[0]["password"];
            return password_verify($pass, $hashedPass);
        }

        return false;
    }

    public function exec_stmt(string $sql, string $param_types, array $params): array
    {
        if (strlen($param_types) !== count($params)) {
            throw new DBError("Número de parámetros incorrecto.");
        }

        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            throw new DBError("Error preparando la consulta: " . $this->con->error);
        }

        // Vincular parámetros correctamente
        if (strlen($param_types) > 0) {
            $stmt->bind_param($param_types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new DBError('Error al ejecutar: ' . $stmt->error);
        }

        $result = $stmt->get_result();

        // Si la consulta es de tipo SELECT, obtener los datos
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        // Si no hay resultado (ejemplo: INSERT, UPDATE, DELETE), devolver éxito
        return [true];
    }

    private function arrayFlatten(array $array): array
    {
        $out = [];

        foreach ($array as $datum) {
            $out = array_merge($out, array_values($datum));
        }

        return $out;
    }
}
