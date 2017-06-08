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
use \NT\SEOBundle\SeoAwareTrait;

/**
 * Company's entity
 *
 * @ORM\Table(name="company_categories")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="CompanyCategoryRepository")
 * @Gedmo\Loggable
 * @Gedmo\Tree(type="nested")
 *
 * @package NTCompaniesBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class CompanyCategory implements PublishWorkflowInterface, SeoAwareInterface
{
    use SeoAwareTrait;
    use PublishWorkflowTrait;
    use \NT\FrontendBundle\Traits\SocialIconsTrait;
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
    * @Gedmo\Versioned
    * @Gedmo\TreeRoot
    * @ORM\Column(name="root", type="integer", nullable=true)
    */
    protected $root;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    protected $rgt;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="CompanyCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="CompanyCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

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
     * @ORM\Column(name="simple_description", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    protected $image;

    /**
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @ORM\ManyToMany(targetEntity="NT\CompaniesBundle\Entity\Company", mappedBy="companyCategories")
     */
    protected $companies;

    /**
     * @ORM\Column(name="is_homepage", type="boolean")
     */
    protected $isHomepage = false;

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
     * @ORM\OneToMany(targetEntity="NT\CompaniesBundle\Entity\CompanyCategoryTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->companies = new ArrayCollection();
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
     * Gets the value of root.
     *
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets the value of root.
     *
     * @param mixed $root the root
     *
     * @return self
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Gets the value of lft.
     *
     * @return mixed
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Sets the value of lft.
     *
     * @param mixed $lft the lft
     *
     * @return self
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Gets the value of lvl.
     *
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Sets the value of lvl.
     *
     * @param mixed $lvl the lvl
     *
     * @return self
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Gets the value of rgt.
     *
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Sets the value of rgt.
     *
     * @param mixed $rgt the rgt
     *
     * @return self
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Gets the value of parent.
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param mixed $parent the parent
     *
     * @return self
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets the value of children.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the value of children.
     *
     * @param mixed $children the children
     *
     * @return self
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
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
        if($this->title === NULL || strlen($this->title) == 0) {
            foreach ($this->translations as $translation) {
                if($translation->getTitle() && strlen($translation->getTitle()) != 0) {
                    return $translation->getTitle();
                }
            }
        }
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

    public function getRoute()
    {
        if ($this->getChildren()->filter(function($item){
            if ($item->getPublishWorkflow()->getIsActive() == true) {
                return true;
            } else {
                return false;
            }
        })) {
            return 'companies_categories_category_view';
        } else {
            return 'companies_categories_list';
        }
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('categorySlug' => $this->getSlug()), $params);
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
     * Gets the value of simpleDescription.
     *
     * @return mixed
     */
    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    /**
     * Sets the value of simpleDescription.
     *
     * @param mixed $simpleDescription the simple description
     *
     * @return self
     */
    public function setSimpleDescription($simpleDescription)
    {
        $this->simpleDescription = $simpleDescription;

        return $this;
    }

    /**
     * Gets the value of companies.
     *
     * @return mixed
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Sets the value of companies.
     *
     * @param mixed $companies the companies
     *
     * @return self
     */
    public function setCompanies($companies)
    {
        $this->companies($companies);

        return $this;
    }

    /**
     * Get the value of Is Homepage
     *
     * @return mixed
     */
    public function getIsHomepage()
    {
        return $this->isHomepage;
    }

    /**
     * Set the value of Is Homepage
     *
     * @param mixed isHomepage
     *
     * @return self
     */
    public function setIsHomepage($isHomepage)
    {
        $this->isHomepage = $isHomepage;

        return $this;
    }
}
