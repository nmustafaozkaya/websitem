<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['contact_name'];
    $telefon = $_POST['contact_telefon'];
    $mail = $_POST['contact_email'];
    $konu = $_POST['contact_konu'];
    $mesaj = $_POST['contact_mesaj'];

    $servername = "localhost";
    $username = "root";
    $password = "34273427Aa";
    $dbname = "mydatabase";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO iletisim (isim, telefon, mail, konu, mesaj) VALUES (:isim, :telefon, :mail, :konu, :mesaj)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':isim', $name);
        $stmt->bindParam(':telefon', $telefon);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':konu', $konu);
        $stmt->bindParam(':mesaj', $mesaj);

        $stmt->execute();

        header("Location: /forms/success.html");
        exit; 

    } catch (PDOException $e) {
        echo "Veritabanı hatası: " . $e->getMessage();
    }
}
?>
