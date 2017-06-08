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

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 *  Entity holding Company's translations
 *
 * @ORM\Entity
 * @ORM\Table(name="company_addresses_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 *
 * @package NTCompaniesBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class CompanyAddressTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NT\CompaniesBundle\Entity\CompanyAddress", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @var string
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    protected $address;

    /**
     * Gets the value of address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the value of address.
     *
     * @param string $address the address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}
