<?php


namespace geoquizz\app\control;
use geoquizz\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use geoquizz\app\model\utilisateur;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;


class ControllerUser
{
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public function register(Request $request, Response $response, $args){
        $input = $request->getParsedBody();
        $exist = utilisateur::where("mail","=",$input['mail'])->first();
        if(!$exist){
            $nom = $input["nom"];
            $prenom = $input["prenom"];
            $mail = $input["mail"];
            $motdepasse = $input["motdepasse"];
            $telephone = $input["telephone"];

            $user = new utilisateur();
            $user->nom = $nom;
            $user->prenom = $prenom;
            $user->mail = $mail;
            $user->motdepasse = password_hash($motdepasse, PASSWORD_DEFAULT);
            $user->telephone = $telephone;

            $user->saveOrFail();

            $token = JWT::encode([
                "iss" => "https://api.tallium.tech/",
                "aud" => "https://api.tallium.tech/",
                "iat" => 1356999524,
                "mail"=>$mail,
                "nbf" => 1357000000
            ],getenv("JWT_SECRET"),'HS512');

            $resparray = array(
                "token"=> $token,
            );

            $response = Writer::jsonResponse($response, 201,$resparray);
            return $response;
        } else {
            $response = Writer::jsonResponse($response, 401,array("error"=>401,"message"=>"compte existant"));
            return $response;
        }

    }

    public function login(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $authorization_header = $request->getHeader("Authorization");
        $credentialsb64 = sscanf($authorization_header[0], "Basic %s");
        $crendentials = base64_decode($credentialsb64[0]);
        $crendentials = explode(":", $crendentials);

        try {
            $user = utilisateur::where("mail", "=", $crendentials[0])->firstOrFail();
            if(password_verify($crendentials[1],$user->motdepasse)){
                $token = JWT::encode([
                    "iss" => "https://api.tallium.tech/",
                    "aud" => "https://api.tallium.tech/",
                    "iat" => 1356999524,
                    "mail"=>$crendentials[0],
                    "nbf" => 1357000000
                ],getenv("JWT_SECRET"),'HS512');
                $element = array(
                    "token" => $token
                );
                $response = Writer::jsonResponse($response,200,$element);

            }else{
                $error = array(
                    "type" => "error",
                    "error" => 401,
                    "message" => "Wrong password"
                );
                $response = Writer::jsonResponse($response,401,$error);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $error = [
                "type" => "error",
                "error" => 401,
                "message" => "Unknown login"
            ];
            $response = Writer::jsonResponse($response, 401, $error);

        }
        return $response;

    }
}