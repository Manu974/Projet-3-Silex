<?php

namespace Blog\DAO;

use Blog\Domain\Comment;

class CommentDAO extends DAO 
{
    /**
     * @var \Blog\DAO\BilletDAO
     */
    private $billetDAO;

    public function setBilletDAO(BilletDAO $billetDAO) {
        $this->billetDAO = $billetDAO;
    }

    /**
     * @var \Blog\DAO\UserDAO
     */
    private $userDAO;

    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }

    /**
     * Returns a comment matching the supplied id.
     *
     * @param integer $id The comment id
     *
     * @return \Blog\Domain\Comment|throws an exception if no matching comment is found
     */
    public function find($id) {
        $sql = "select * from t_comment where com_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No comment matching id " . $id);
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


        // art_id is not selected by the SQL query
        // The user won't be retrieved during domain objet construction
        $sql = "select com_id, com_pseudo, com_dateofpost, com_content,billet_id, parent, status, report from t_comment where com_pseudo=? order by com_id";
          $result = $this->getDb()->fetchAll($sql, array($userId));

      
        // Convert query result to an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $commentId = $row['com_id'];
            $comment = $this->buildDomainObject($row);
            // The associated user is defined for the constructed comment
            $comment->setPseudo($user);
            $comments[$commentId] = $comment;
        }
        return $comments;
    }


    /**
     * Return a list of all comments for an billet, sorted by date (most recent last).
     *
     * @param integer $billetId The billet id.
     *
     * @return array A list of all comments for the billet.
     */
    public function findAllByBillet($billetId) {
        // The associated billet is retrieved only once
        $billet = $this->billetDAO->find($billetId);

        // art_id is not selected by the SQL query
        // The billet won't be retrieved during domain objet construction
        $sql = "select com_id, com_pseudo, com_dateofpost, com_content, parent, status, report from t_comment where billet_id=? order by com_id";
        $result = $this->getDb()->fetchAll($sql, array($billetId));

        // Convert query result to an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $comId = $row['com_id'];
            $comment = $this->buildDomainObject($row);
            // The associated billet is defined for the constructed comment
            $comment->setBillet($billet);
            $comments[$comId] = $comment;
        }
        return $comments;
    }

    /**
     * Returns a list of all comments, sorted by date (most recent first).
     *
     * @return array A list of all comments.
     */
    public function findAll() {
        $sql = "select * from t_comment order by com_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['com_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }

    /**
     * Returns a list of all comments, sorted by date (most recent first).
     *
     * @return array A list of all comments.
     */
    public function findAllUnpublish() {
        return $this->getDb()->query("select COUNT(*) as status from t_comment WHERE status=0")->fetchColumn();
        
    }

    /**
     * Returns a list of all comments, sorted by date (most recent first).
     *
     * @return array A list of all comments.
     */
    public function findAllpublish() {
        return $this->getDb()->query("select COUNT(*) as status from t_comment WHERE status=1")->fetchColumn();     
    }

    /**
     * Returns a list of all comments, sorted by date (most recent first).
     *
     * @return array A list of all comments.
     */
    public function findAllreport() {
        return $this->getDb()->query("select COUNT(*) as report from t_comment WHERE report=0")->fetchColumn();     
    }

    /**
     * Saves a comment into the database.
     *
     * @param \Blog\Domain\Comment $comment The comment to save
     */
    public function save(Comment $comment) {
        $commentData = array(
            'billet_id' => $comment->getBillet()->getId(),
            'com_pseudo' => $comment->getPseudo()->getId(),
            'com_content' => $comment->getContent(),
            'com_dateofpost' => $comment->getDateofpost()->format('Y-m-d H:i:s'),
            'status'=> $comment->getStatus(),
            'parent'=> $comment->getParent(),
            'report'=> $comment->getReport()
            
            );
        if ($comment->getId()) {
            // The comment has already been saved : update it
            $this->getDb()->update('t_comment', $commentData, array('com_id' => $comment->getId()));
        } else {
            // The comment has never been saved : insert it
            $this->getDb()->insert('t_comment', $commentData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    /**
     * Saves a comment into the database.
     *
     * @param \Blog\Domain\Comment $comment The comment to save
     */
    public function update(Comment $comment) {
        $commentData = array(
            'billet_id' => $comment->getBillet()->getId(),
            'com_pseudo' => $comment->getPseudo()->getId(),
            'com_content' => $comment->getContent(),
            'com_dateofpost' => $comment->getDateofpost(),
            'status'=> $comment->getStatus(),
            'parent'=> $comment->getParent(),
            'report'=> $comment->getReport()
            );
        if ($comment->getId()) {
            // The comment has already been saved : update it
            $this->getDb()->update('t_comment', $commentData, array('com_id' => $comment->getId()));
        } else {
            // The comment has never been saved : insert it
            $this->getDb()->insert('t_comment', $commentData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    /**
     * Removes all comments for an article
     *
     * @param $billetId The id of the article
     */
    public function deleteAllByBillet($billetId) {
        $this->getDb()->delete('t_comment', array('billet_id' => $billetId));
    }

    /**
     * Removes a comment from the database.
     *
     * @param @param integer $id The comment id
     */
    public function delete($id) {
        // Delete the comment
        $this->getDb()->delete('t_comment', array('com_id' => $id));
    }

    /**
     * Removes all comments for a user
     *
     * @param integer $userId The id of the user
     */
    public function deleteAllByUser($userId) {

        $this->getDb()->delete('t_comment', array('com_pseudo' => $userId));
    }


    /**
     * Creates an Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \Blog\Domain\Comment
     */
    protected function buildDomainObject(array $row) {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setDateofpost($row['com_dateofpost']);
        $comment->setContent($row['com_content']);
        $comment->setParent($row['parent']);
        $comment->setStatus($row['status']);
        $comment->setReport($row['report']);
        
        if (array_key_exists('billet_id', $row)) {
            // Find and set the associated billet
            $billetId = $row['billet_id'];
            $billet = $this->billetDAO->find($billetId);
            $comment->setBillet($billet);
        }

         if (array_key_exists('com_pseudo', $row)) {
            // Find and set the associated author
            $userId = $row['com_pseudo'];
            $user = $this->userDAO->find($userId);
            $comment->setPseudo($user);
        }
        
        return $comment;
    }
}