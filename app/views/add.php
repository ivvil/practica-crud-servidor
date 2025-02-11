<?php

namespace App\Crud;

session_start();

$method = $_SERVER["REQUEST_METHOD"];

if (!isset($_SESSION["user"]) || !isset($_SESSION["pass"])){
    header("Location: /login");
    die();
}

function is_required($val):bool{
    return $val !== "NO";
}

function is_autoinrement($val): bool{
    return str_contains($val,"auto_increment");
}

function fetch_type($val){

    return match (true){
        str_starts_with($val, "varchar") => "text",
        str_starts_with($val, "int") => "number",
        default => "text"
    };
     
}

function get_table_schema($tablename):array {
    $db = DB::getInstance();
    $fields = $db->exec_stmt("DESCRIBE " . $db->con->real_escape_string($tablename), "", []);
    $table_schema = [];

    foreach ($fields as $field){
        if (is_autoinrement($field['Extra'])) continue;
        
        $table_schema[] = [
            "name" => $field["Field"],
            "type" => fetch_type($field['Type']),
            "required" => is_required($field['Null']), 
        ];

    }

    return $table_schema;
}

if ($method == 'POST') {
    $table_schema = get_table_schema($tablename);

    $db = DB::getInstance();

    var_dump($_POST);
    /*
        Funciona pero:
         Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child row: a foreign key constraint fails 
            (`tienda`.`producto`, CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`familia`) REFERENCES `familia` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE) 
            in /var/www/html/class/DB.php on line 231

         mysqli_sql_exception: Cannot add or update a child row: a foreign key constraint fails 
            (`tienda`.`producto`, CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`familia`) REFERENCES `familia` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE) 
            in /var/www/html/class/DB.php on line 231


        (( Yo creo que asi vale ya o que?  )) Hay que mirar las foraneas y poner los valores con un select o algo un option estaria bien pero uffffffffffffffffffffffffffffff :,c
         La funcion para sacar las foraneas ya esta, pero no se si vale la pena 
         yo lo dejaria asi y me iria a dormir
         Le enviamos la url del repo y lo cambiamos si no mas adelante?
         Va
         hago commit  ok espera
    */
    $db->add_fila($tablename, $_POST);

    
    var_dump("Big Chungus Amongus" . $tablename);
    

} else {
    $twig = Templates::getInstance();

    echo $twig->load("layouts/new.html.twig", [
        'tablename' => $tablename,
        'table' =>  get_table_schema($tablename)
    ]); 
}


