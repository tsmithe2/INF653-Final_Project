<?php 
    class Author 
    {
        private $conn;
        private $table = "authors";
        
        public $id;
        public $author;

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
            $this->author = $row["author"];
            $authors = file_get_contents("https://inf653-final-project.herokuapp.com/api/authors/");
            $authors_obj = json_decode($authors, true);

            for ($i = 0; $i < count($authors_obj); $i++)
            {
                if ($row["id"] == $authors_obj[$i]["id"])
                {
                    $this->author = $authors_obj[$i]["author"];
                    break;
                }
            }
        }

        public function create() 
        {
            $query = "INSERT INTO " . $this->table . " SET author = :author";
            $stmt = $this->conn->prepare($query);
            $this->author = htmlspecialchars(strip_tags($this->author));

            if (1 === preg_match('~[0-9]~', $this->author) || empty($this->author)) //if the author contains any numbers
            {
              return false;
            }

            $stmt->bindParam(":author", $this->author);

            if($stmt->execute()) 
            {
              return true;
            }
            
            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function update() 
        {
            $query = "UPDATE " . $this->table . " SET author = :author WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->id = htmlspecialchars(strip_tags($this->id));

            $authors = file_get_contents("https://inf653-final-project.herokuapp.com/api/authors/");
            $authors_obj = json_decode($authors, true);

            if ($this->id > count($authors_obj) || 1 === preg_match('~[0-9]~', $this->author) || $this->id <= 0 || empty($this->author))
            {
                return false;
            }

            $stmt->bindParam(":author", $this->author);
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

            $authors = file_get_contents("https://inf653-final-project.herokuapp.com/api/authors/");
            $authors_obj = json_decode($authors, true);

            if ($this->id > count($authors_obj) || $this->id <= 0)
            {
                return false;
            }

            $stmt->bindParam(":id", $this->id);

            if($stmt->execute()) 
            {
                //change the auto increment to the number of items in the databse.
                $query = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = " . count($authors_obj);
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }
?>