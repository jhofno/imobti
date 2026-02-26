<?php

class Conexao
{

  //variavel de conexao
  private $host = '10.91.45.44';
  private $bd = 'imobiliaria';
  private $user = 'admin';
  private $pass = '123456';

  public function conexao()
  {

    try {
      //configura a conexao com as informacoes do banco de dados

      $pdo = new PDO("mysql:host={$this->host};bdname={$this->bd};charset=utf8", $this->user, $this->pass);
      //configura o PDO para lancar os erros como excessoes   
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $pdo;
    } catch (PDOException $err) {
      die("Erro na conexão" . $err->getMessage());
      return null;
    }
  }
}
?>
