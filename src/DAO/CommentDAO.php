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
        $sql = "select com_id, com_pseudo, com_dateofpost, com_content, parent from t_comment where billet_id=? order by com_id";
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
     * Saves a comment into the database.
     *
     * @param \Blog\Domain\Comment $comment The comment to save
     */
    public function save(Comment $comment) {
        $commentData = array(
            'billet_id' => $comment->getBillet()->getId(),
            'com_pseudo' => $comment->getPseudo(),
            'com_content' => $comment->getContent(),
            'com_dateofpost' => $comment->getDateofpost()->format('Y-m-d H:i:s')
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
     * Creates an Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \Blog\Domain\Comment
     */
    protected function buildDomainObject(array $row) {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setPseudo($row['com_pseudo']);
        $comment->setDateofpost($row['com_dateofpost']);
        $comment->setContent($row['com_content']);
        $comment->setParent($row['parent']);

        if (array_key_exists('billet_id', $row)) {
            // Find and set the associated billet
            $billetId = $row['billet_id'];
            $billet = $this->billetDAO->find($billetId);
            $comment->setBillet($billet);
        }
        
        return $comment;
    }
}