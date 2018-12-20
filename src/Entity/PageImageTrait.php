<?php

namespace PiedWeb\CMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PiedWeb\CMSBundle\Entity\MediaInterface as Media;

trait PageImageTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\CMSBundle\Entity\MediaInterface")
     */
    protected $mainImage;

    /**
     * @var ArrayCollection
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="PiedWeb\CMSBundle\Entity\PageHasMediaInterface", mappedBy="page",cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position":"ASC"})
     */
    protected $pageHasMedias;

    public function __construct_image()
    {
        $this->pageHasMedias = new ArrayCollection();
    }

    public function setPageHasMedias($pageHasMedias)
    {
        $this->pageHasMedias = new ArrayCollection();
        foreach ($pageHasMedias as $pageHasMedia) {
            $this->addPageHasMedia($pageHasMedia);
        }
    }

    public function getPageHasMedias()
    {
        return $this->pageHasMedias;
    }

    public function addPageHasMedia(PageHasMedia $pageHasMedia)
    {
        $pageHasMedia->setPage($this);
        $this->pageHasMedias[] = $pageHasMedia;
    }

    public function removePageHasMedia(PageHasMedia $pageHasMedia)
    {
        $this->pageHasMedias->removeElement($pageHasMedia);
    }

    public function getMainImage(): ?Media
    {
        return $this->mainImage;
    }

    public function setMainImage(?Media $mainImage): self
    {
        // TODO: Déplacer en Assert pour éviter une erreur dégueu ?!
        if (null !== $mainImage && null === $mainImage->getWidth()) {
            throw new \Exception('mainImage must be an Image. Media imported is not an image');
        }

        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        if (!$this->images) {
            $this->images = new ArrayCollection();
            foreach ($this->pageHasMedias as $p) {
                if (null !== $p->getMedia()) {
                    $this->images[] = $p->getMedia();
                }
            }
        }

        return $this->images;
    }

    public function issetImage()
    {
        if ($this->getImages()->count() > 0) {
            return true;
        }

        return false;
    }
}
