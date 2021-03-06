<?php

require_once('MySQL.php');

class Cliente {

    private $id;
    private $username;
    private $password;
    private $email;
    private $name;
    private $lastname;
    private $address;


    private function __construct($username, $password, $email, $name, $lastname, $address){
        $this->username= $username;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->address = $address;
       
    }

    public function id(){ 
        return $this->id; 
    }


    public function username(){
        return $this->username;
    }

    public function email(){
        return $this->email;
    }

    public function name(){
        return $this->name;
    }

    public function password(){
        return $this->password;
    }

    public function lastname(){
        return $this->lastname;
    }

    public function address(){
        return $this->address;
    }


    //Revisar esta función
    public function cambiaPassword($nuevoPassword){
        $this->password = password_hash($nuevoPassword, PASSWORD_DEFAULT);
    }


    /* Devuelve un objeto cliente con la información del cliente $username,
     o false si no lo encuentra*/
    public static function buscacliente($username){
        $app = MySQL::getInstanceMySQL();
        $conn = $app->conexionMySQL();

        $query = sprintf("SELECT * FROM cliente U WHERE U.user = '%s'", $conn->real_escape_string($username));
        $rs = $conn->query($query);
        $result = false;

        if ($rs) {
            if ( $rs->num_rows == 1) {
                $fila = $rs->fetch_assoc();
                $user = new Cliente($fila['user'], $fila['pass'], $fila['email'],
                                    $fila['nombre'], $fila['apellidos'], $fila['direccion']);
                $user->id = $fila['id'];


                $result = $user;
            }
            $rs->free();
        } else {
            echo "Error al consultar en la BD: (" . $conn->errno . ") " . utf8_encode($conn->error);
            exit();
        }

        return $result;
    }

    /*Comprueba si la contraseña introducida coincide con la del cliente.*/
    public function compruebaPassword($password){
        return password_verify($password, $this->password);
    }

    /* Devuelve un objeto cliente si el cliente existe y coincide su contraseña. En caso contrario,
     devuelve false.*/
    public static function login($username, $password){
        $user = self::buscacliente($username);

        if ($user && $user->compruebaPassword($password)) {
        
            $_SESSION['login'] = true;
            $_SESSION['cliente'] = serialize($user);

            return $user;
        }
    
        return false;
    }
    
    /* Crea un nuevo cliente con los datos introducidos por parámetro. */
    public static function crea($username, $password, $email, $name, $lastname, $address){
        $user = self::buscacliente($username);

        if ($user) {
            return false;
        }
        $user = new cliente($username, password_hash($password, PASSWORD_DEFAULT), $email, 
                            $name, $lastname, $address);


        return self::guarda($user);
    }
    
    
    public static function guarda($cliente){
        if ($cliente->id() !== null) {
            return self::actualiza($cliente);
        }
        return self::inserta($cliente);
    }
    
    private static function inserta($cliente){
        $app = MySQL::getInstanceMySQL();
        $conn = $app->conexionMySQL();
        $query=sprintf("INSERT INTO cliente(user, pass, email, nombre, apellidos, direccion) 
                        VALUES('%s', '%s', '%s', '%s', '%s', '%s')"
            , $conn->real_escape_string($cliente->username)
            , $conn->real_escape_string($cliente->password)
            , $conn->real_escape_string($cliente->email)
            , $conn->real_escape_string($cliente->name)
            , $conn->real_escape_string($cliente->lastname)
            , $conn->real_escape_string($cliente->address));

        if ( $conn->query($query) ){
            $cliente->id = $conn->insert_id;
        } else {
            echo "Error al insertar en la BD: (" . $conn->errno . ") " . utf8_encode($conn->error);
            exit();
        }
        return $cliente;
    }
    
    private static function actualiza($cliente){
        $app = MySQL::getInstanceMySQL();
        $conn = $app->conexionMySQL();


        $query=sprintf("UPDATE cliente U 
                        SET U.pass='%s'
                        WHERE U.id='%s' "
            , $conn->real_escape_string($cliente->password())
            , $cliente->id());

        if ( $conn->query($query) ) {
            if ( $conn->affected_rows != 1) {
    
                echo "No se ha podido actualizar la contraseña";
                exit();
            }
            else{
                return $cliente;
            }
        } else {
            echo "Error al actualizar en la BD: (" . $conn->errno . ") " . utf8_encode($conn->error);
            exit();
        }
    }

    public function getPedidos(){

        $app = MySQL::getInstanceMySQL();
        $conn = $app->conexionMySQL();

        $query = sprintf("SELECT * 
                          FROM realiza R JOIN pedido P
                          WHERE R.id_cliente = $this->id 
                            AND R.id_pedido = P.id");

        $rs = $conn->query($query);
        $result = false;

        if ($rs) {

            if ( $rs->num_rows > 0) {
                for($i = 0; $i < $rs->num_rows; $i++){
                    $fila = $rs->fetch_assoc();

                    $pedidos[] = array(
                        'id' => $fila['id'],
                        'fecha' => $fila['fecha'],
                    );
                }

                $rs->free();
                return $pedidos;
            }

        } else {
            echo "Error al consultar en la BD: (" . $conn->errno . ") " . utf8_encode($conn->error);
            exit();
        }

        return $result;
    }

    public static function eliminarPedido(){

        echo "Hola";
    }
}
