<?php
 require_once(__DIR__."/../config/conexao.php");

class Usuario
{

    private int     $id;
    private string  $nome;
    private string  $email;
    private string  $senhaHash;
    private int     $idPerfil;
    private bool    $ativo;


    public function __construct(
        ?int $id = 0,
        string $nome,
        string $email,
        string $senhaHash,
        int $idPerfil,
        ?bool $ativo = true,
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senhaHash = password_hash($senhaHash, PASSWORD_DEFAULT);
        $this->idPerfil = $idPerfil;
        $this->ativo = $ativo;
    }

    // Métodos mágicos Get e Set
    public function __get(string $prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
        throw new  Exception("Propriedade {$prop} não existe");
    }


    public function __set(string $prop, $valor)
    {
        switch ($prop) {
            case "id":
                $this->id = (int)$valor;
                break;
            case "nome":
                $this->nome = trim($valor);
                break;
            case "email":
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("E-mail inválido");
                }
                $this->email = $valor;
            break;
            case "senhaHash":
                $this->senhaHash = password_hash($valor, PASSWORD_DEFAULT);
            break;
            case "idPerfil":
                $this->idPerfil = $valor;
            break;
            case "ativo":
                $this->ativo = (bool)$valor;
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida");
        }
    }

    private static function getConexao(){
        return (new Conexao())->conexao();
    }

    public function inserir() {
        $pdo = self::getConexao();

        $sql = "INSERT INTO `usuarios` (`nome`, `email`, `senha`, `ativo`, `id_perfil`) 
        VALUES (:nome, :email, :senha, :ativo, :idPerfil)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nome'     => $this->nome,
            ':email'    => $this->email,
            ':senha'    => $this->senhaHash,
            ':ativo'    => $this->ativo,
            ':idPerfil' =>  $this->idPerfil,
        
        ]);        
        
        $ultimoId = $pdo->lastInsertId();

        echo $ultimoId;
    }
}




$usuario1 = new Usuario(nome: "Apollo", email: "apollo@gmail.com", senhaHash: 123, idPerfil:1, ativo: true );

$usuario1->nome = "Apollo David";
echo $usuario1->nome;
echo $usuario1->senhaHash ."<br>";
$usuario1->senhaHash = 123;
echo $usuario1->senhaHash;

?>