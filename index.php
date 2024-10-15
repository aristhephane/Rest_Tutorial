<?php
header("Content-Type: application/json");

// Connexion à la base de données MySQL
$host = 'localhost';
$dbname = 'recettes_db';
$user = 'rest'; // utilisateur créer pour gérer la BD
$password = 'cVl0]7ytMq4k'; 
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["message" => "Erreur de connexion à la base de données", "error" => $e->getMessage()]));
}

// Les requêtes GET (Pour récupérer toutes les recettes)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $pdo->query("SELECT * FROM recettes");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($recipes);
}

// Les requêtes POST (Pour ajouter une recette)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO recettes (nom, ingredients, temps_preparation) VALUES (:nom, :ingredients, :temps_preparation)");
    $stmt->execute([
        ':nom' => $input['nom'],
        ':ingredients' => implode(", ", $input['ingredients']), // Stocker comme texte séparé par des virgules
        ':temps_preparation' => $input['temps_preparation']
    ]);
    echo json_encode(["message" => "Recette ajoutée avec succès"]);
}

// Les requêtes PUT (Mise à jour d'une recette)
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE recettes SET nom = :nom, ingredients = :ingredients, temps_preparation = :temps_preparation WHERE id = :id");
    $stmt->execute([
        ':nom' => $input['nom'],
        ':ingredients' => implode(", ", $input['ingredients']), // Stocker comme texte séparé par des virgules
        ':temps_preparation' => $input['temps_preparation'],
        ':id' => $id
    ]);
    echo json_encode(["message" => "Recette mise à jour avec succès"]);
}

// Ls requêtes DELETE (Pour suppriimer une recette)
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM recettes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(["message" => "Recette supprimée avec succès"]);
}


