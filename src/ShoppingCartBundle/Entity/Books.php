<?php

namespace ShoppingCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Finder\Finder;

/**
 * Books
 *
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="ShoppingCartBundle\Repository\BooksRepository")
 */
class Books
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Cart", mappedBy="book")
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="books")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="sold_count", type="integer")
     */
    private $soldCount;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Books
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Books
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price.
     *
     * @param float $price
     *
     * @return Books
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set soldCount.
     *
     * @param int $soldCount
     *
     * @return Books
     */
    public function setSoldCount($soldCount)
    {
        $this->soldCount = $soldCount;

        return $this;
    }

    /**
     * Get soldCount.
     *
     * @return int
     */
    public function getSoldCount()
    {
        return $this->soldCount;
    }

    /**
     * @param mixed $category
     * @return Books
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }


    /**
     * @return mixed
     */
    public function getImage()
    {
        return "0005.jpg";
    }

    /**
     * @return mixed
     */
    public function getRates()
    {
        $rates['discount'] = 15;
        $rates['actual_rate'] = $this->getPrice() - ($this->getPrice() * $rates['discount'] / 100);
        return $rates;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        if (isset($this->cart[0]))
            return $this->cart;
        else
            return false;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }


}
