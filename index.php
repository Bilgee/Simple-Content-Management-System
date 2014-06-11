<?php
require_once 'config.php';

$article = new Article();
$article->displaySomething();
?>  
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="assets/style.css" />
    </head>
    <body>
        <h1>Hi</h1>
        <div class="container">
            <a href="index.php" id="logo">CMS</a>
            <ol>
                <li><a href="article.php?id=1">Article Title</a> 
                    - <small>posted 10th Jan</small></li>
            </ol>
        </div>
        <?php
        ?>
    </body>
</html>
