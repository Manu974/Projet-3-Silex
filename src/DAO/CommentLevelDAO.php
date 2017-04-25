<?php

namespace Blog\DAO;

use Blog\Domain\CommentLevel;

class CommentLevelDAO extends DAO
{

    /**
     * @var \Blog\DAO\CommentDAO
     */
    private $commentDAO;

    public function setCommentDAO(CommentDAO $commentDAO) {
        $this->commentDAO = $commentDAO;
    }

    /**
     * Return a list of all commentLevel, sorted by date (most recent first).
     *
     * @return array A list of all commentLevel.
     */
    public function findAll() {
        $sql = "select * from t_comment_level order by cl_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $commentLevel = array();
        foreach ($result as $row) {
            $commentLevelId = $row['cl_id'];
            $commentLevel[$commentLevelId] = $this->buildDomainObject($row);
        }
        return $commentLevel;
    }

     /**
     * Returns an article matching the supplied id.
     *
     * @param integer $id
     *
     * @return \Blog\Domain\CommentLevel|throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "select * from t_comment_level where cl_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No billet matching id " . $id);
    }

    /**
     * Return a list of all billets for an comment, sorted by date (most recent last).
     *
     * @param integer $commentId The comment id.
     *
     * @return array A list of all billets for the comment.
     */
    public function findAllByComment($commentId) {
        // The associated comment is retrieved only once
        $comment = $this->commentDAO->find($commentId);


        $sql = "select cl_id, parent, level where parent=? order by cl_id";
          $result = $this->getDb()->fetchAll($sql, array($commentId));

      
        // Convert query result to an array of domain objects
        $commentsLevel = array();
        foreach ($result as $row) {
            $commentLevelId = $row['cl_id'];
            $commentLevel = $this->buildDomainObject($row);
            // The associated comment is defined for the constructed commentLevel
            $commentLevel->setParent($comment);
            $commentsLevel[$commentLevelId] = $commentLevel;
        }
        return $commentsLevel;
    }


     /**
     * Saves an commentLevel into the database.
     *
     * @param \Blog\Domain\CommentLevel $commentLevel The commentlevel to save
     */
    public function save(CommentLevel $commentLevel) {
        $commentLevelData = array(
            'cl_id' => $commentLevel->getId(),
            'parent' => $commentLevel->getParent()->getId(),
            'level' => $commentLevel->getLevel()
            );

        if ($commentLevel->getId()) {
            // The commentLevel has already been saved : update it
            $this->getDb()->update('t_comment_level', $commentLevelData, array('cl_id' => $commentLevel->getId()));
        } else {
            // The commentLevel has never been saved : insert it
            $this->getDb()->insert('t_comment_level', $commentLevelData);
            // Get the id of the newly created commentLevel and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $commentLevel->setId($id);
        }
    }

    /**
     * Removes an billet from the database.
     *
     * @param integer $id The billet id.
     */
    public function delete($id) {
        // Delete the billet
        $this->getDb()->delete('t_comment_level', array('cl_id' => $id));
    }

    /**
     * Removes all comments for a user
     *
     * @param integer $userId The id of the user
     */
    public function deleteAllByUser($userId) {

        $this->getDb()->delete('t_billet', array('author' => $userId));
    }

    

    /**
     * Creates an Billet object based on a DB row.
     *
     * @param array $row The DB row containing Billet data.
     * @return \Blog\Domain\CommentLevel
     */
    protected function buildDomainObject(array $row) {
        $commentLevel = new CommentLevel();
        $commentLevel->setId($row['cl_id']);
        $commentLevel->setLevel($row['level']);
        

         if (array_key_exists('parent', $row)) {
            // Find and set the associated author
            $commentId = $row['parent'];
            $comment = $this->commentDAO->find($commentId);
            $commentLevel->setParent($comment);
        }
        return $commentLevel;
    }
}