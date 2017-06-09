<?php
namespace NT\BannersBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use NT\SEOBundle\SeoAwareInterface;
use NT\PublishWorkflowBundle\PublishWorkflowInterface;

/**
 * Event
 *
 * @ORM\Table(name="banners")
 * @ORM\Entity(repositoryClass="BannersRepository")
 * @Gedmo\Loggable
 */
class Banner implements PublishWorkflowInterface
{
    use \NT\PublishWorkflowBundle\PublishWorkflowTrait;
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="BannersPages", mappedBy="banner")
     */
    protected $bannersPages;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

     /**
     * @ORM\OneToMany(targetEntity="BannerTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->bannersPages = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProductCategory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ProductCategory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return $this->getTitle() ? : 'n/a';
    }

    /**
    * Get image
    * @return
    */
    public function getImage()
    {
        return $this->image;
    }

    /**
    * Set image
    * @return $this
    */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param mixed $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of bannersPages.
     *
     * @return mixed
     */
    public function getBannersPages()
    {
        return $this->bannersPages;
    }

    /**
     * Sets the value of bannersPages.
     *
     * @param mixed $bannersPages the banners pages
     *
     * @return self
     */
    public function setBannersPages($bannersPages)
    {
        $this->bannersPages = $bannersPages;

        return $this;
    }
}
