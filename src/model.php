<?php

// Return all billets
function getBillets() {
    $bdd = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'blog_user', 'Ovnir@$');
    $billets = $bdd->query('select * from t_billet order by billet_id desc');
    return $billets;
}
