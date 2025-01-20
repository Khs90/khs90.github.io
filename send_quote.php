<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si tous les champs sont remplis
    if (isset($_POST['name'], $_POST['company_name'], $_POST['project_name'], $_POST['description'], $_POST['phone'], $_FILES['file'])) {
        // Récupérer les données du formulaire
        $name = $_POST['name'];
        $company_name = $_POST['company_name'];
        $project_name = $_POST['project_name'];
        $description = $_POST['description'];
        $phone = $_POST['phone'];

        // Vérification du fichier téléchargé
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo "Erreur lors du téléchargement du fichier. Code d'erreur : " . $_FILES['file']['error'];
            exit;
        }

        // Récupérer les informations sur le fichier
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_path = 'uploads/' . $file_name; // Dossier pour stocker le fichier

        // Créer le dossier uploads s'il n'existe pas
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Déplacer le fichier téléchargé dans le dossier uploads
        move_uploaded_file($file_tmp, $file_path);

        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = 'mail.maquette-maroc.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'maquette@maquette-maroc.com';
            $mail->Password = 'khalid-2019';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Informations sur l'email
            $mail->setFrom('maquette@maquette-maroc.com', 'Nom de votre site');
            $mail->addAddress('maqtop.contacts@gmail.com'); // Destinataire
            $mail->addAddress('maquette.contacte@gmail.com'); // Destinataire

            // Contenu du message
            $mail->isHTML(true);
            $mail->Subject = 'Demande de devis';
            $mail->Body = "
                <h3>Demande de devis</h3>
                <p><strong>Nom:</strong> $name</p>
                <p><strong>Entreprise:</strong> $company_name</p>
                <p><strong>Nom du projet:</strong> $project_name</p>
                <p><strong>Description:</strong> $description</p>
                <p><strong>Numéro:</strong> $phone</p>
            ";

            // Ajouter le fichier en pièce jointe
            if (file_exists($file_path)) {
                $mail->addAttachment($file_path, $file_name); // Ajouter le fichier
            }

            // Envoi de l'email
            $mail->send();
            echo 'L\'email a été envoyé avec succès.';

            // Supprimer le fichier après envoi
            unlink($file_path);
        } catch (Exception $e) {
            echo "Erreur : {$mail->ErrorInfo}";
        }
    } else {
        echo 'Tous les champs doivent être remplis et le fichier doit être téléchargé.';
    }
}
?>