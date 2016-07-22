<?php
namespace Entity;

/**
 * @Document
 */
class Article
{
    /**
     * @Id
     */
    private $id;

    /**
     * @Field(type="string")
     */
    private $title;

    /**
     * @Field(type="string")
     */
    private $content;

    // --------------------------------------------------------------------

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }
}
