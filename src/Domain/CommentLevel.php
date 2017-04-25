<?php

namespace Blog\Domain;

class CommentLevel 
{
    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    


    /**
     * Comment level.
     *
     * @var integer
     */
    private $parent;

   
    /**
     * Comment level.
     *
     * @var integer
     */
    private $level;

    /**
     * Associated billet.
     *
      * @var \Blog\Domain\Comment
     */
    private $comment;



    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
   
    

    public function getParent() {
        return $this->parent;
    }
  
    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }


    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    /**
     * Returns user.
     *
     * @return \Blog\Domain\Comment
     */
    public function getComment() {
        return $this->comment;
    }
    /**
     * Returns user.
     *
     * @return \Blog\Domain\Comment
     */
    public function setComment(Comment $comment) {
        $this->comment = $comment;
        return $this;
    }
}