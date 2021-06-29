<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $nb_jour = $_POST['nb_jour'];
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $contact = $_POST['contact'];
        $contact = str_replace("'","\'",$contact);
        $id_user_app = $_POST['id_user_app'];
        $id_vehicule = $_POST['id_vehicule'];
        $date_heure = date('Y-m-d H:i:s');

        $insertdata = mysqli_query($con, "insert into tj_location_vehicule(nb_jour,date_debut,date_fin,contact,statut,id_vehicule,id_user_app,creer)
        values($nb_jour,'$date_debut','$date_fin','$contact','en cours',$id_vehicule,$id_user_app,'$date_heure')");
        $id = mysqli_insert_id($con);

        if ($insertdata > 0) {
            $response['msg']['etat'] = 1;
        } else {
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>