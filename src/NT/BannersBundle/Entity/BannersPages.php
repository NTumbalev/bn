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
 * @ORM\Table(name="banners_pages")
 * @ORM\Entity(repositoryClass="BannersPagesRepository")
 */
class BannersPages
{
    public static $positions = array(
        'left'     => 'Ляво',
        'right'    => 'Дясно',
        'top'      => 'Горе',
        'homepage' => 'Начална страница',
    );

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="position", type="string", length=255)
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Banner", inversedBy="bannersPages")
     * @ORM\JoinColumn(name="banner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $banner;

    /**
     * @ORM\ManyToOne(targetEntity="\NT\CompaniesBundle\Entity\CompanyCategory")
     */
    protected $page;

    /**
     * @var integer
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @var bool
     * @ORM\Column(name="is_main", type="boolean")
     */
    protected $isMain = false;

    /**
     * @var bool
     * @ORM\Column(name="on_all_categories", type="boolean")
     */
    protected $onAllCategories = false;

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
        return (string)$this->getId() ? : 'n/a';
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
     * Gets the value of rank.
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Sets the value of rank.
     *
     * @param integer $rank the rank
     *
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Gets the value of banner.
     *
     * @return mixed
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Sets the value of banner.
     *
     * @param mixed $banner the banner
     *
     * @return self
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Gets the value of page.
     *
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the value of page.
     *
     * @param mixed $page the page
     *
     * @return self
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Gets the value of position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the value of position.
     *
     * @param string $position the position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Gets the value of isMain.
     *
     * @return mixed
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * Sets the value of isMain.
     *
     * @param mixed $isMain the is main
     *
     * @return self
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOnAllCategories()
    {
        return $this->onAllCategories;
    }

    /**
     * @param bool $onAllCategories
     *
     * @return self
     */
    public function setOnAllCategories($onAllCategories)
    {
        $this->onAllCategories = $onAllCategories;

        return $this;
    }
}
