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
    * Return a list of all billets for an user, sorted by date (most recent last).
    *
    * @param integer $userId The user id.
    *
    * @return array A list of all billets for the user.
    */
    public function findAllByUser($userId) {
        // The associated user is retrieved only once
        $user = $this->userDAO->find($userId);
        // billet_id is not selected by the SQL query
        // The user won't be retrieved during domain objet construction
        $sql = "select billet_id, billet_title, billet_content, billet_publication,author from t_billet where author=? order by billet_id";
        $result = $this->getDb()->fetchAll($sql, array($userId));

        // Convert query result to an array of domain objects
        $billets = array();

        foreach ($result as $row) {
            $billetId = $row['billet_id'];
            $billet = $this->buildDomainObject($row);
            // The associated user is defined for the constructed billet
            $billet->setAuthor($user);
            $billets[$billetId] = $billet;
        }

        return $billets;
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
        } 

        else {
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
        $this->getDb()->delete('t_billet', ['billet_id' => $id]);
    }

    /**
    * Removes all comments for a user
    *
    * @param integer $userId The id of the user
    */
    public function deleteAllByUser($userId) {

        $this->getDb()->delete('t_billet', ['author' => $userId]);
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