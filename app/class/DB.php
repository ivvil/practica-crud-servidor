<?php

namespace App\Crud;

use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;

class DB
{

    public $con;

    public function __construct()
    {
        $host = $_ENV['HOST'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['PASSWORD'];
        $db = $_ENV['DATABASE'];


        try {
            $this->con = new mysqli($host, $user, $pass, $db);
            if ($this->con->connect_error) {
                throw new DBError("Connection failed: " . $this->con->connect_error);
            }
        } catch (mysqli_sql_exception $e) {
            throw new DBError("Database error: " . $e->getMessage());
        }
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

        $stmt = $this->con->prepare('SHOW TABLES');
        if (!$stmt) {
            throw new DBError('Prepare failed: ' . $this->con->error);   
        }

        if (!$stmt->execute()) {
            throw new DBError('Execute failed: ' . $stmt->error);
        }

        
        $stmt->bind_result($tableName);

        while ($stmt->fetch()) {
            $tablas[] = $tableName;
        }

        $stmt->close();

        return $tablas;
    }

    // Retorna un array con las filas de una tabla
    public function get_filas(string $sentencia): array
    {
        $filas = [];
        if (!$this->con) {
            return false;
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
        $stmt = $this->con->prepare('INSERT INTO usuarios (nombre, password) VALUES (?, ?)');
        if (!$stmt) {
            throw new DBError('Prepare failed: ' . $this->con->error);
        }

        $stmt->bind_param('s', $nombre, 's', password_hash($pass, PASSWORD_DEFAULT));

        if (!$stmt->execute()) {
            throw new DBError('Execute failed: ' . $stmt->error);
        }
    }

    public function existe_usuario(string $nombre): bool
    {
        $stmt = $this->con->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        if (!$stmt) {
            throw new DBError("Prepare failed: " . $this->con->error);
        }

        $stmt->bind_param('s', $nombre);

        if (!$stmt->execute()) {
            throw new DBError('Execute failed: ' . $stmt->error);
        }

        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    public function comprobar_contrasegna(string $nombre, string $pass): bool
    {
        $sql = "SELECT password FROM usuarios WHERE nombre = ?";
        $stmt = $this->ejecuta_sentencia($sql, [$nombre]);
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            throw new DBError("User not found.");
        }

        $stmt->bind_result($hashedPass);
        $stmt->fetch();

        return password_verify($pass, $hashedPass);
    }
}
