<?php
include "config.php";
include "utils.php";
$dbConn =  connect($db);
$raw = file_get_contents("php://input");
$datos = json_decode($raw);
//GET
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['id']))
    {
      
      $sql = $dbConn->prepare("SELECT * FROM libros where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
      exit();

    }
    elseif(isset($_GET['accion']) && $_GET['accion'] === 'tocho'){

      $sql = $dbConn->prepare("SELECT * FROM libros ORDER BY paginas DESC LIMIT 1");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll());
      exit();

    }
    elseif(isset($_GET['accion']) && $_GET['accion'] === 'obras'){
      
      $sql = $dbConn->prepare("SELECT * FROM libros WHERE autor = :autor");
      $sql->bindValue(':autor', $datos->autor);
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll());
      exit();
  
    }
    else {
      
      $sql = $dbConn->prepare("SELECT * FROM libros");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();

  }
}

//POST
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO libros
          (tItulo, autor, paginas, ISBN)
          VALUES
          (:titulo, :autor, :paginas, :ISBN)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $dbConn->lastInsertId();
    if($postId)
    {
      $input['id'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
   }
  
}

//DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  $id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM libros where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}

//PUT
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['id'];
    $fields = getParams($input);
    $sql = "
          UPDATE libros
          SET $fields
          WHERE id='$postId'
           ";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}
header("HTTP/1.1 400 Bad Request");
?>