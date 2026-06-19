<?php
   $db_host = getenv('DB_HOST') ?: 'db';
   $db_name = getenv('DB_NAME') ?: 'realestate';
   $db_user = getenv('DB_USER') ?: 'root';
   $db_pass = getenv('DB_PASSWORD') ?: 'rootpass';
   
   $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   function create_unique_id(){
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < 20; $i++) {
          $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
      }
      return $randomString;
  }
?>
