<?php

class connect() {
    public function dbConnect() {
        try {
            $dns = 'mysql:host=localhost;dbname=art_ss';
            $username = 'root';
            $password = '';
            
            $db = new PDO($dns, $username, $password);
        
            return $db;
        }
        catch(PDOExeption $e) {
            echo 'Error'. $e;
        }
        
    }
}

?>