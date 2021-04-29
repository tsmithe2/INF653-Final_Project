<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    require("../../config/Database.php");
    require("../../models/Author.php");

    $database = new Database();
    $db = $database->connect();
    $author = new Author($db);
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->author))
    {
        $author->author = $data->author;
    }
    
    if ($author->create())
    {
        echo json_encode(array("message" => "Author Created"));
    } 
    else 
    {
        echo json_encode(array("message" => "Author Not Created"));
    }
?>