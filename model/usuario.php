<?php
require_once(__DIR__ . "/../config/conexao.php");

class Usuario
{

    private ?int     $id;
    private string  $nome;
    private string  $email;
    private string  $senhaHash;
    private int     $idPerfil;
    private bool    $ativo;

    private ?string $perfilNome = null;


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
        $this->senhaHash = $senhaHash;
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

            case "perfilNome";
                $this->perfilNome = $valor;
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida");
        }
    }

    private static function getConexao()
    {
        return (new Conexao())->conexao();
    }

    public function inserir()
    {
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

        if ($ultimoId <= 0) {
            throw new Exception("Não Foi Possivel inserir o usuário");
        }
        return $ultimoId;
    }

    public static function listar()
    {
        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario, 
            u.nome, 
            u.email, 
            u.ativo, 
            u.id_perfil,
            p.nome_perfil as perfil_nivel 
        FROM usuarios as u
        INNER JOIN perfis p on p.id_perfil = u.id_perfil
        ORDER BY u.nome";

        $stmt = $pdo->query($sql);

        $usuarios = [];

        //Retornando todos usuarios

        $usuarios = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $usuario = new Usuario(
                id: $row['id_usuario'],
                nome: $row['nome'],
                email: $row['email'],
                senhaHash: "",
                idPerfil: $row['id_perfil'],
                ativo: (bool)$row['ativo']
            );

            $usuario->perfilNome = $row['perfil_nivel'];

            array_push($usuarios, $usuario);

            $lista = $usuarios;
        }

        return $usuarios;
    }

    public static function buscarPorId(int $id)
    {

        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario, 
            u.nome, 
            u.email, 
            u.ativo, 
            u.id_perfil,
            p.nome_perfil as perfil_nivel 
            FROM usuarios as u
            INNER JOIN perfis p on p.id_perfil = u.id_perfil
            WHERE u.id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$row){
            throw new Exception("Id do usuário Não encontrado");
            return null;
        }

        $usuario = new Usuario(
            id: $row['id_usuario'],
            nome: $row['nome'],
            email: $row['email'],
            senhaHash: "",
            idPerfil: $row['id_perfil'],
            ativo: (bool)$row['ativo']
        );

        $usuario->perfilNome = $row['perfil_nivel'];


        return $usuario;
    }
}

// ///--- Teste inserir usuario
// $usuario1 = (new Usuario(
//     nome:       "Natan", 
//     email:      "natan@gmail.com", 
//     senhaHash:  "123", 
//     idPerfil:   "3", 
//     ativo:      true 
// ))->inserir();

// $usuario1->nome = "Apollo David";
// echo $usuario1->nome;
// echo $usuario1->senhaHash ."<br>";
// $usuario1->senhaHash = 123;
// echo $usuario1->senhaHash;


echo "<pre>";

try{
    print_r(Usuario::buscarPorId(1));

}catch(Exception $err){
    echo $err->getMessage();
}
