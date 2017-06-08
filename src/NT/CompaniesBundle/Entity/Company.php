<?php
/**
 * This file is part of the NTCompaniesBundle.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use NT\PublishWorkflowBundle\PublishWorkflowTrait;
use NT\PublishWorkflowBundle\PublishWorkflowInterface;
use NT\SEOBundle\SeoAwareInterface;
use NT\SEOBundle\SeoAwareTrait;

/**
 * Company's entity
 *
 * @ORM\Table(name="companies")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="CompanyRepository")
 * @Gedmo\Loggable
 *
 * @package NTCompaniesBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class Company implements PublishWorkflowInterface, SeoAwareInterface
{
    use SeoAwareTrait;
    use PublishWorkflowTrait;
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="CompanyAddress", mappedBy="company", cascade={"persist", "remove"})
     */
    protected $addresses;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $gallery;

    /**
     * @ORM\ManyToMany(targetEntity="NT\CompaniesBundle\Entity\CompanyCategory", inversedBy="companies")
     * @ORM\JoinTable(name="companies_categories")
     */
    protected $companyCategories;

    /**
     * @ORM\Column(name="is_top", type="boolean")
     */
    protected $isTop = false;

    /**
     * @ORM\ManyToOne(targetEntity="NT\LocationsBundle\Entity\Location", inversedBy="companies")
     */
    protected $location;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="NT\CompaniesBundle\Entity\CompanyTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->companyCategories = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param  string        $title
     * @return Entertainment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime     $createdAt
     * @return Entertainment
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
     * @param  \DateTime     $updatedAt
     * @return Entertainment
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
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param string $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param mixed $image the image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Gets the value of gallery.
     *
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Sets the value of gallery.
     *
     * @param mixed $gallery the gallery
     *
     * @return self
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getRoute()
    {
        if ($this->getCompanyCategories()->count() > 0) {
            return 'companies_category_company_view';
        } else {
            return 'company_without_category';
        }
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
    }

    /**
     * Gets the value of rank.
     *
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Sets the value of rank.
     *
     * @param mixed $rank the rank
     *
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }


    /**
     * Get the value of CompanyCategories
     *
     * @return mixed
     */
    public function getCompanyCategories()
    {
        return $this->companyCategories;
    }

    /**
     * Set the value of CompanyCategories
     *
     * @param mixed companyCategories
     *
     * @return self
     */
    public function setCompanyCategories($companyCategories)
    {
        $this->companyCategories = $companyCategories;

        return $this;
    }

    /**
     * Gets the value of isTop.
     *
     * @return mixed
     */
    public function getIsTop()
    {
        return $this->isTop;
    }

    /**
     * Sets the value of isTop.
     *
     * @param mixed $isTop the is top
     *
     * @return self
     */
    public function setIsTop($isTop)
    {
        $this->isTop = $isTop;

        return $this;
    }

    /**
     * Gets the value of location.
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the value of location.
     *
     * @param mixed $location the location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Gets the value of addresses.
     *
     * @return mixed
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Sets the value of addresses.
     *
     * @param mixed $addresses the addresses
     *
     * @return self
     */
    public function setAddresses($addresses)
    {
        foreach ($addresses as $address) {
            $this->addAddress($address);
        }

        return $this;
    }

    public function addAddress(CompanyAddress $address)
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCompany($this);
        }
    }

    public function removeAddress(CompanyAddress $address)
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            $address->setCompany(null);
        }
    }
}
