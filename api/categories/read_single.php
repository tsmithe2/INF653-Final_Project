<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    require("../../config/Database.php");
    require("../../models/Category.php");

    $database = new Database();
    $db = $database->connect();
    $category = new Category($db);
    $category->id = isset($_GET["id"]) ? $_GET["id"] : die();
    $categories = file_get_contents("https://inf653-final-project.herokuapp.com/api/categories/");
    $categories_obj = json_decode($categories, true);

    //if id is invalid then show error message.
    if ($_GET["id"] > count($categories_obj) || $_GET["id"] <= 0)
    {
        echo json_encode(array("message" => "No Category Found"));
        return false;
    }

    $category->read_single();
    $category_arr = array("id" => $category->id, "category" => $category->category);
    print_r(json_encode($category_arr));
?>