<?php

namespace Blog\Domain;

class Comment 
{
    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    /**
     * Comment pseudo.
     *
     * @var string
     */
    private $pseudo;

    /**
     * Comment dateofpost.
     *
     * @var datetime
     */
    private $dateofpost;

    /**
     * Comment content.
     *
     * @var string
     */
    private $content;

    /**
     * Associated billet.
     *
     * @var \Blog\Domain\Billet
     */
    private $billet;


    /**
     * Comment parent.
     *
     * @var integer
     */
    private $parent;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getPseudo() {
        return $this->pseudo;
    }

    public function setPseudo($pseudo) {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getDateofpost() {
        return $this->dateofpost;
    }

    public function setDateofpost($dateofpost) {
        $this->dateofpost = $dateofpost;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getBillet() {
        return $this->billet;
    }

    public function setBillet(Billet $billet) {
        $this->billet = $billet;
        return $this;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }
}