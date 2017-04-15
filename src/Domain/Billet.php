<?php

namespace Blog\Domain;

class Billet 
{
    /**
     * Billet id.
     *
     * @var integer
     */
    private $id;

    /**
     * Billet title.
     *
     * @var string
     */
    private $title;

     /**
     * Associated user.
     *
     * @var \Blog\Domain\User
     */
    private $author;

    /**
     * Billet content.
     *
     * @var string
     */
    private $content;

    /**
     * Billet datetime.
     *
     * @var datetime
     */
    private $publication;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns user.
     *
     * @return \Blog\Domain\User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Returns user.
     *
     * @return \Blog\Domain\User
     */
    public function setAuthor(User $author) {
        $this->author = $author;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getPublication() {
        return $this->publication;
    }

    public function setPublication($publication) {
        $this->publication = $publication;
        return $this;
    }
}