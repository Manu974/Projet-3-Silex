<?php

namespace Blog\DAO;

use Blog\Domain\Billet;

class BilletDAO extends DAO
{

    /**
     * Return a list of all billets, sorted by date (most recent first).
     *
     * @return array A list of all billets.
     */
    public function findAll() {
        $sql = "select * from t_billet order by billet_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $billets = array();
        foreach ($result as $row) {
            $billetId = $row['billet_id'];
            $billets[$billetId] = $this->buildDomainObject($row);
        }
        return $billets;
    }

     /**
     * Returns an article matching the supplied id.
     *
     * @param integer $id
     *
     * @return \Blog\Domain\Billet|throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "select * from t_billet where billet_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $id);
    }

    

    /**
     * Creates an Billet object based on a DB row.
     *
     * @param array $row The DB row containing Billet data.
     * @return \Blog\Domain\Billet
     */
    protected function buildDomainObject(array $row) {
        $billet = new Billet();
        $billet->setId($row['billet_id']);
        $billet->setTitle($row['billet_title']);
        $billet->setContent($row['billet_content']);
        $billet->setPublication($row['billet_publication']);
        return $billet;
    }
}