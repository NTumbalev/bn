<?php

namespace NT\BannersBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * NT\BannersBundle\Entity\BannerTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="banner_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 */
class BannerTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NT\BannersBundle\Entity\Banner", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

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
}