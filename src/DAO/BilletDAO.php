<?php

namespace Blog\DAO;

use Blog\Domain\Billet;

class BilletDAO extends DAO
{

    /**
     * @var \Blog\DAO\UserDAO
     */
    private $userDAO;

    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }

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
            throw new \Exception("No billet matching id " . $id);
    }


     /**
     * Saves an billet into the database.
     *
     * @param \Blog\Domain\Billet $billet The billet to save
     */
    public function save(Billet $billet) {
        $billetData = array(
            'billet_title' => $billet->getTitle(),
            'billet_content' => $billet->getContent(),
            'billet_publication' => $billet->getPublication()->format('Y-m-d H:i:s'),
            'author' =>$billet->getAuthor()->getId()
            );

        if ($billet->getId()) {
            // The billet has already been saved : update it
            $this->getDb()->update('t_billet', $billetData, array('billet_id' => $billet->getId()));
        } else {
            // The billet has never been saved : insert it
            $this->getDb()->insert('t_billet', $billetData);
            // Get the id of the newly created billet and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $billet->setId($id);
        }
    }

    /**
     * Removes an billet from the database.
     *
     * @param integer $id The billet id.
     */
    public function delete($id) {
        // Delete the billet
        $this->getDb()->delete('t_billet', array('billet_id' => $id));
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

         if (array_key_exists('author', $row)) {
            // Find and set the associated author
            $userId = $row['author'];
            $user = $this->userDAO->find($userId);
            $billet->setAuthor($user);
        }
        return $billet;
    }
}