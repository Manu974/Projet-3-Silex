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
     * Associated billet.
     *
      * @var \Blog\Domain\Comment
     */
    private $parent;

   
    /**
     * Comment level.
     *
     * @var integer
     */
    private $level;



    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
   
    
    /**
     * Returns user.
     *
     * @return \Blog\Domain\Comment
     */
    public function getParent() {
        return $this->parent;
    }
    /**
     * Returns user.
     *
     * @return \Blog\Domain\Comment
     */
    public function setParent(Comment $parent) {
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
}