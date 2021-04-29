<?php 
    class Category 
    {
        private $conn;
        private $table = "categories";
        
        public $id;
        public $category;

        public function __construct($db) 
        {
            $this->conn = $db;
        }

        public function read() 
        {
            $query = "SELECT * FROM " . $this->table;

            if (isset($_GET["limit"]))
            {
                $query .= " LIMIT " . $_GET["limit"];
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        public function read_single() 
        {
            $query = "SELECT * FROM " . $this->table . " WHERE id = " . $_GET["id"];
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->category = $row['category'];
            $categories = file_get_contents("http://localhost/final_project/api/categories/");
            $categories_obj = json_decode($categories, true);

            for ($i = 0; $i < count($categories_obj); $i++)
            {
                if ($row["id"] == $categories_obj[$i]["id"])
                {
                    $this->category = $categories_obj[$i]["category"];
                    break;
                }
            }
        }

        public function create() 
        {
            $query = "INSERT INTO " . $this->table . " SET category = :category";
            $stmt = $this->conn->prepare($query);

            $this->category = htmlspecialchars(strip_tags($this->category));

            if (1 === preg_match('~[0-9]~', $this->category) || empty($this->category)) //if the category contains any numbers
            {
                return false;
            }

            $stmt->bindParam(":category", $this->category);

            if($stmt->execute()) 
            {
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function update() 
        {
            $query = "UPDATE " . $this->table . " SET category = :category WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->id = htmlspecialchars(strip_tags($this->id));

            $categories = file_get_contents("http://localhost/final_project/api/categories/");
            $categories_obj = json_decode($categories, true);

            if ($this->id > count($categories_obj) || 1 === preg_match('~[0-9]~', $this->category) || $this->id <= 0 || empty($this->category))
            {
                return false;
            }

            $stmt->bindParam(":category", $this->category);
            $stmt->bindParam(":id", $this->id);

            if($stmt->execute()) 
            {
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function delete() 
        {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));

            $categories = file_get_contents("http://localhost/final_project/api/categories/");
            $categories_obj = json_decode($categories, true);

            if ($this->id > count($categories_obj) || $this->id <= 0)
            {
                return false;
            }

            $stmt->bindParam(":id", $this->id);

            if($stmt->execute()) 
            {
                //change the auto increment to the number of items in the databse.
                $query = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = " . count($categories_obj);
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }
?>