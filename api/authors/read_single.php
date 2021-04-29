<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    require("../../config/Database.php");
    require("../../models/Author.php");

    $database = new Database();
    $db = $database->connect();
    $author = new Author($db);
    $author->id = isset($_GET["id"]) ? $_GET["id"] : die();

    $authors = file_get_contents("http://localhost/final_project/api/authors/");
    $authors_obj = json_decode($authors, true);

    //if id is invalid then show error message.
    if ($_GET["id"] > count($authors_obj) || $_GET["id"] <= 0)
    {
        echo json_encode(array("message" => "No Author Found"));
        return false;
    }

    $author->read_single();
    $author_arr = array("id" => $author->id, "author" => $author->author);
    print_r(json_encode($author_arr));
?>