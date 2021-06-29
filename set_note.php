<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_user_app = $_POST['id_user_app'];
        $id_conducteur = $_POST['id_conducteur'];
        $note_value = $_POST['note_value'];
        // $id_user_app = 4;
        // $id_conducteur = 2;
        // $note_value = 3;
        $date_heure = date('Y-m-d H:i:s');

        $chknote = mysqli_query($con, "select * from tj_note where id_user_app=$id_user_app AND id_conducteur=$id_conducteur");
        if (mysqli_num_rows($chknote) > 0) {
            $updatedata = mysqli_query($con, "update tj_note set niveau=$note_value, modifier='$date_heure' where id_conducteur=$id_conducteur AND id_user_app=$id_user_app");

            // Nb avis conducteur
            $sql_nb_avis = "SELECT count(id) as nb_avis, sum(niveau) as somme FROM tj_note WHERE id_conducteur=$id_conducteur";
            $result_nb_avis = mysqli_query($con,$sql_nb_avis);
            $row_nb_avis = mysqli_fetch_assoc($result_nb_avis);

            $somme = $row_nb_avis['somme'];
            $nb_avis = $row_nb_avis['nb_avis'];
            $moyenne = $somme/$nb_avis;

            // Note conducteur
            $sql_note = "SELECT niveau FROM tj_note WHERE id_conducteur=$id_conducteur AND id_user_app=$id_user_app";
            $result_note = mysqli_query($con,$sql_note);
            $row_note = mysqli_fetch_assoc($result_note);

            $row['nb_avis'] = $row_nb_avis['nb_avis'];
            if(mysqli_num_rows($result_note) > 0)
                $row['niveau'] = $row_note['niveau'];
            else
                $row['niveau'] = "";
            $row['moyenne'] = $moyenne;
            $response['msg']['etat'] = 1;
            $response['msg']['note'] = $row;
        } else {
                $insertdata = mysqli_query($con, "insert into tj_note(niveau,id_conducteur,id_user_app,statut,creer)
                values($note_value,$id_conducteur,$id_user_app,'yes','$date_heure')");
                $id = mysqli_insert_id($con);
                if ($insertdata > 0) {
                    $row = [];

                    // Nb avis conducteur
                    $sql_nb_avis = "SELECT count(id) as nb_avis, sum(niveau) as somme FROM tj_note WHERE id_conducteur=$id_conducteur";
                    $result_nb_avis = mysqli_query($con,$sql_nb_avis);
                    $row_nb_avis = mysqli_fetch_assoc($result_nb_avis);
        
                    $somme = $row_nb_avis['somme'];
                    $nb_avis = $row_nb_avis['nb_avis'];
                    $moyenne = $somme/$nb_avis;
        
                    // Note conducteur
                    $sql_note = "SELECT niveau FROM tj_note WHERE id_conducteur=$id_conducteur AND id_user_app=$id_user_app";
                    $result_note = mysqli_query($con,$sql_note);
                    $row_note = mysqli_fetch_assoc($result_note);
        
                    $row['nb_avis'] = $row_nb_avis['nb_avis'];
                    if(mysqli_num_rows($result_note) > 0)
                        $row['niveau'] = $row_note['niveau'];
                    else
                        $row['niveau'] = "";
                    $row['moyenne'] = $moyenne;
                    $response['msg']['note'] = $row;
                    $response['msg']['etat'] = 1;
                } else {
                    $response['msg']['etat'] = 2;
                }
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>