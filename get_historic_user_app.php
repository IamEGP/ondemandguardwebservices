<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    $months = array ("January"=>'Jan',"February"=>'Fev',"March"=>'Mar',"April"=>'Avr',"May"=>'Mai',"June"=>'Jun',"July"=>'Jul',"August"=>'Aou',"September"=>'Sep',"October"=>'Oct',"November"=>'Nov',"December"=>'Déc');
    
    // if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $id_user_app = $_POST['id_user_app'];

        $sql = "SELECT r.id,r.id_user_app,r.latitude_depart,r.longitude_depart,r.latitude_arrivee,r.longitude_arrivee,
        r.statut,r.statut_course,r.id_conducteur_accepter,r.creer,u.nom,u.prenom,r.distance/*,c.nom as nomConducteur,c.prenom as prenomConducteur*/,
        r.montant,r.duree
        FROM tj_requete r, tj_user_app u/*, tj_conducteur c*/
        WHERE r.id_user_app=u.id /*AND r.id_conducteur_accepter=c.id*/ AND r.id_user_app=$id_user_app AND r.statut_course='clôturer'
        ORDER BY r.id DESC";
        $result = mysqli_query($con,$sql);
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $id_conducteur = $row['id_conducteur_accepter'];
            if($id_conducteur != 0){
                // Conducteur
                $sql_cond = "SELECT nom as nomConducteur,prenom as prenomConducteur FROM tj_conducteur WHERE id=$id_conducteur";
                $result_cond = mysqli_query($con,$sql_cond);
                $row_cond = mysqli_fetch_assoc($result_cond);

                // Nb avis conducteur
                $sql_nb_avis = "SELECT count(id) as nb_avis, sum(niveau) as somme FROM tj_note WHERE id_conducteur=$id_conducteur";
                $result_nb_avis = mysqli_query($con,$sql_nb_avis);
                if(mysqli_num_rows($result_nb_avis) > 0){
                    $row_nb_avis = mysqli_fetch_assoc($result_nb_avis);
                    $somme = $row_nb_avis['somme'];
                    $nb_avis = $row_nb_avis['nb_avis'];
                    if($nb_avis != "0")
                        $moyenne = $somme/$nb_avis;
                    else
                        $moyenne = "0";
                }else{
                    $somme = "0";
                    $nb_avis = "0";
                    $moyenne = "0";
                }

                // Note conducteur
                $sql_note = "SELECT niveau FROM tj_note WHERE id_conducteur=$id_conducteur AND id_user_app=$id_user_app";
                $result_note = mysqli_query($con,$sql_note);
                $row_note = mysqli_fetch_assoc($result_note);

                $row['nomConducteur'] = $row_cond['nomConducteur'];
                $row['prenomConducteur'] = $row_cond['prenomConducteur'];
                $row['nb_avis'] = $row_nb_avis['nb_avis'];
                if(mysqli_num_rows($result_note) > 0)
                    $row['niveau'] = $row_note['niveau'];
                else
                    $row['niveau'] = "";
                $row['moyenne'] = $moyenne;
            }else{
                $row['nomConducteur'] = "";
                $row['prenomConducteur'] = "";
                $row['nb_avis'] = "";
                $row['niveau'] = "";
                $row['moyenne'] = "";
            }
            
            $row['creer'] = date("d", strtotime($row['creer']))." ".$months[date("F", strtotime($row['creer']))].". ".date("Y", strtotime($row['creer']));

            $output[] = $row;
        }
        
        if(mysqli_num_rows($result) > 0){
            $response['msg'] = $output;
            $response['msg']['etat'] = 1;
        }else{
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    // }
?>