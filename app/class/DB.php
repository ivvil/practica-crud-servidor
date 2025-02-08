<?php

namespace App\Crud;

use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;

class DB
{
    private static $instance = null;
    public $con;

    private function __construct()
    {
        $host="mysql";
        $user="alumno";
        $pass="alumno";
        $db="tienda";
        /* $ROOT_PASSWORD="root_password"   */          ;

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
        if (self::$instance === null)
        {
            self::$instance = new DB();
        }

        return self::$instance;
    }

    /*
     * Este método tendría que investigar en el diccionario de datos
     * Devolverá qué campos de esta tabla son claves foráneas
     * */
    public function get_foraneas(string $tabla): array
    {
        return [];
    }


    public function get_tablas(): array
    {
        $tablas = [];

        $res = $this->exec_stmt('SHOW TABLES', "", []);
        if (!$res) {
            throw new DBError('Prepare failed: ' . $this->con->error);   
        }

        return $tablas;
    }

    // Retorna un array con las filas de una tabla
    public function get_filas(string $tabla): array
    {
        $filas = [];
        $res = $this->exec_stmt("SELECT * FROM ?", "s", [ $tabla ]);
        // TETAS
        if (!$res) {
            return [];
        }

        return $filas;
    }

    //Borra una fila de una tabla dada su código
    //Retorna un mensaje diciendo si lo ha podido borrar o no
    public function borrar_fila(string $table, int $cod): string
    {
        if (!$this->con) {
            return "Error en la conexión";
        }
    }

    public function close()
    {
        $this->con->close();
    }

    // Añade una fila cuyos valores se pasan en un array.
    //Tengo el nombre de la tabla y el array ["nombre_Campo"=>"valor"]
    public function add_fila(string $tabla, array $campos)
    {


        if (!$this->con) {
            return false;
        }
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
            if (count($res) > 1){
                throw new DBError("QUE COÑO MAS DE 1 USUARIO");
            }
            $hashedPass = $res[0]["password"];
            return password_verify($pass, $hashedPass);
        }
        
        return false;

    }

    private function exec_stmt(string $sql, string $param_types, array $params): array
{
    if (strlen($param_types) !== count($params)) {
        throw new DBError("Número de parámetros incorrecto.");
    }

    $stmt = $this->con->prepare($sql);
    if (!$stmt) {
        throw new DBError("Error preparando la consulta: " . $this->con->error);
    }

    // Vincular parámetros correctamente
    if (strlen($param_types) > 0){
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

}
