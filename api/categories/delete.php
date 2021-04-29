<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    require("../../config/Database.php");
    require("../../models/Category.php");

    $database = new Database();
    $db = $database->connect();
    $category = new Category($db);
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id))
    {
        $category->id = $data->id;
    }

    if ($category->delete()) 
    {
        echo json_encode(array("message" => "Category Deleted"));
    } 
    else 
    {
        echo json_encode(array("message" => "Category Not Deleted"));
    }
?>