<?php

namespace Blog\DAO;

use Doctrine\DBAL\Connection;
use Blog\Domain\Billet;

class BilletDAO
{
    /**
     * Database connection
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection The database connection object
     */
    public function __construct(Connection $db) {
        $this->db = $db;
    }

    /**
     * Return a list of all billets, sorted by date (most recent first).
     *
     * @return array A list of all billets.
     */
    public function findAll() {
        $sql = "select * from t_billet order by billet_id desc";
        $result = $this->db->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $billets = array();
        foreach ($result as $row) {
            $billetId = $row['billet_id'];
            $billets[$billetId] = $this->buildBillet($row);
        }
        return $billets;
    }

    /**
     * Creates an Billet object based on a DB row.
     *
     * @param array $row The DB row containing Billet data.
     * @return \Blog\Domain\Billet
     */
    private function buildBillet(array $row) {
        $billet = new Billet();
        $billet->setId($row['billet_id']);
        $billet->setTitle($row['billet_title']);
        $billet->setAuthor($row['billet_author']);
        $billet->setContent($row['billet_content']);
        $billet->setPublication($row['billet_publication']);
        return $billet;
    }
}