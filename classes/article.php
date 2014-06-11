<?php
/*
 * DB:articles 
 * Field
 * article_id
 * article_title
 * article_content
 * article_timestamp
 */
class Article {

// Properties
    public $id = null;                  //The article ID from the database
    public $timestamp = null;   //When the article was published
    public $title = null;               //Full title of the article
    public $summary = null;             //A short summary of the article
    public $content = null;             //The HTML content of the article
    private $pdo = null;                //Private variable for PDO 
    

    public function __construct($data = array()) {
        if (isset($data['article_id'])) {
            $this->id = (int) $data['article_id'];
        }
        if (isset($data['article_timestamp'])) {
            $this->article_timestamp = (int) $data['article_timestamp'];
        }
        if (isset($data['article_title'])) {
            $this->title = preg_replace("/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['article_title']);
        }
        if (isset($data['article_summary'])) {
            $this->summary = preg_replace("/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['article_summary']);
        }
        if (isset($data['article_content'])) {
            $this->content = $data['article_content'];
        }
    }
    
    public function displaySomething(){
        $this->pdo = getPDO();
        $pd = getPDO();
        $sql = 'select * from articles';
        foreach ($this->pdo->query($sql) as $row) {
            print($row['article_id'].'<br>');
            print($row['article_title']);
        }       
        $this->pdo = null;
    }
    /**
     * Sets the object's properties using the edit form post values in the supplied array
     * @param assoc The form post values
     */
    public function storeFormValues($params) {
        $this->__construct($params);                    // Store all the parameters

        // Parse and store the publication date
        if (isset($params['article_timestamp'])) {
            $timestamp = explode('-', $params['article_timestamp']);

            if (count($timestamp) == 3) {
                list ( $y, $m, $d ) = $timestamp;
                $this->article_timestamp = mktime(0, 0, 0, $m, $d, $y);
            }
        }
        $this->pdo = null;
    }
    /**
     * Returns an Article object matching the given article ID
     * @param int The article ID
     * @return Article|false The article object, or false if the record was not found or there was a problem
     */
    public static function getById($id) {
        $this->pdo = getPDO();
        $sql = "SELECT *, UNIX_TIMESTAMP(article_timestamp) AS article_timestamp FROM articles WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        $this->pdo = null;
        if ($row){
            return new Article($row);
        }
        $this->pdo = null;
    }

    /**
     * Returns all (or a range of) Article objects in the DB
     * @param int Optional The number of rows to return (default=all)
     * @param string Optional column by which to order the articles (default="article_timestamp DESC")
     * @return Array|false A two-element array : results => array, a list of Article objects; totalRows => Total number of articles
     */
    public static function getList($numRows = 1000000, $order = "article_timestamp DESC") {
        $this->pdo = getPDO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(article_timestamp) AS article_timestamp FROM articles
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
        $st->execute();
        $list = array();

        while ($row = $st->fetch()) {
            $article = new Article($row);
            $list[] = $article;
        }

        // Now get the total number of articles that matched the criteria
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $this->pdo->query($sql)->fetch();
        $this->pdo = null;
        return ( array("results" => $list, "totalRows" => $totalRows[0]) );
    }

    /**
     * Inserts the current Article object into the database, and sets its ID property.
     */
    public function insert() {

        // Does the Article object already have an ID?
        if (!is_null($this->id))
            trigger_error("Article::insert(): INSERT ERROR ( $this->id).", E_USER_ERROR);

        // Insert the Article
        $this->pdo = getPDO();
        $sql = "INSERT INTO articles ( article_timestamp, title, summary, content ) VALUES ( FROM_UNIXTIME(:article_timestamp), :title, :summary, :content )";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":article_timestamp", $this->article_timestamp, PDO::PARAM_INT);
        $st->bindValue(":title", $this->title, PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, PDO::PARAM_STR);
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
        $this->pdo = null;
    }

    /**
     * Updates the current Article object in the database.
     */
    public function update() {

        // Does the Article object have an ID?
        if (is_null($this->id))
            trigger_error("Article::update(): UPDATE ERROR.", E_USER_ERROR);

        // Update the Article
        $this->pdo = getPDO();
        $sql = "UPDATE articles SET article_timestamp=FROM_UNIXTIME(:article_timestamp), title=:title, summary=:summary, content=:content WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":article_timestamp", $this->article_timestamp, PDO::PARAM_INT);
        $st->bindValue(":title", $this->title, PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();
        $this->pdo = null;
    }

    /**
     * Deletes the current Article object from the database.
     */
    public function delete() {

        // Does the Article object have an ID?
        if (is_null($this->id))
            trigger_error("Article::delete(): DELETE ERROR.", E_USER_ERROR);

        // Delete the Article
        $this->pdo = getPDO();
        $st = $this->pdo->prepare("DELETE FROM articles WHERE id = :id LIMIT 1");
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();
        $this->pdo = null;
    }
}

?>