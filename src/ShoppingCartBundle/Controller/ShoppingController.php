<?php

namespace ShoppingCartBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Export\ExportException;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ShoppingCartBundle\Entity\Books;
use ShoppingCartBundle\Entity\Cart;
use ShoppingCartBundle\Entity\Category;
use ShoppingCartBundle\Entity\Coupon;
use ShoppingCartBundle\Repository\BooksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ShoppingController extends Controller
{
    var $session_id;

    function __construct()
    {
        $session = new Session();
        if (!$session->get('session_id'))
            $this->session_id = $session->set('session_id', md5(time()));
        else
            $this->session_id = $session->get('session_id');

    }

    /**
     * @Route("/")
     */
    function index()
    {
        $categories = $this->getDoctrine()
            ->getManager()
            ->getRepository(Category::class)
            ->createQueryBuilder('s')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);


        return $this->render('@ShoppingCart/index.html.twig', ["categories" => $categories]);
    }

    /**
     * @Route("/checkout")
     */
    function indexCheckout()
    {

        $em = $this->getDoctrine()
            ->getManager()
            ->getRepository(Cart::class);

        $cart = $em->getCart($this->session_id);
        $price = $em->getPrice($this->session_id);


        $session = new Session();
        $coupon = $session->get('coupon');

        return $this->render('@ShoppingCart/checkout.html.twig', ["carts" => $cart, 'price' => $price, 'coupon' => $coupon]);
    }


    /**
     * @Route("/ajax/get/books")
     *
     * @param Request $request
     * @return string
     */
    function getBooks(Request $request)
    {
        $em = $this->getDoctrine()->getManager()->getRepository(Books::class);
        $books = $em->getBooks($request->query->all());

        return $this->render('@ShoppingCart/book.html.twig', ["books" => $books, 'em' => $em]);
    }

    /**
     * @Route("/ajax/add/cart")
     * @Method({"post"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    function addToCart(Request $request)
    {

        $bookId = $request->get('bookId');
        $book = $em = $this->getDoctrine()->getManager()->getRepository(Books::class)->find($bookId);
        $Date = new \DateTime();

        $em = $this->getDoctrine()->getManager()->getRepository(Cart::class);


        $found = $em->isBookExist($book, $this->session_id); //check book already exist

        $em = $this->getDoctrine()->getManager();

        try {
            if ($found) {
                $cart = $found->setQty($found->getQty() + 1);
            } else {
                $cart = new Cart();
                $cart->setBook($book);
                $cart->setQty(1);
                $cart->setSessionId($this->session_id);
                $cart->setCreatedDate($Date);
            }
            $em->persist($cart);
            $em->flush();

            return new JsonResponse(['status' => true, "sessionId" => $this->session_id, "msg" => "Successfully added to the cart!"]);

        } catch (\Exception $e) {
            return new JsonResponse(['status' => false, "msg" => $e->getMessage()]);
        }
    }

    /**
     * @Route("/ajax/remove/cart")
     * @Method({"post"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    function removeFromCart(Request $request)
    {
        $bookId = $request->get('bookId');
        $book = $em = $this->getDoctrine()->getManager()->getRepository(Books::class)->find($bookId);

        $em = $this->getDoctrine()->getManager()->getRepository(Cart::class);
        $found = $em->isBookExist($book, $this->session_id); //check book already exist

        if ($found) {

            $em = $this->getDoctrine()->getManager();

            try {
                $em->remove($found);
                $em->flush();

                return new JsonResponse(['status' => true, "sessionId" => $this->session_id, "msg" => "Successfully remove from the cart!"]);

            } catch (\Exception $e) {
                return new JsonResponse(['status' => false, "msg" => $e->getMessage()]);
            }
        } else {
            return new JsonResponse(['status' => false, "msg" => "This book is not in the cart!"]);
        }
    }

    /**
     * @Route("ajax/get/cart")
     *
     *
     * @return JsonResponse
     */
    function getCart()
    {
        $em = $this->getDoctrine()->getManager()->getRepository(Cart::class);
        $count = 0;

        $price = $em->getPrice($this->session_id);

        foreach ($em->getCart($this->session_id) as $cartItem){
            $count += $cartItem->getQty();
        }

        return new JsonResponse(['count'=>$count,'price'=>$price]);
    }

    /**
     * @Route("ajax/set/coupon")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    function setCoupon(Request $request)
    {
        $coupon = $request->request->get('coupon');

        $em = $this->getDoctrine()->getManager()->getRepository(Coupon::class);

        $couponDiscount = $em->findBy(['coupon' => $coupon]);
        if (!empty($couponDiscount[0])) {
            $session = new Session();
            $this->session_id = $session->set('coupon', $coupon);

            return new JsonResponse([
                "msg" => "Done"
            ]);
        }
        else{
            return new JsonResponse([
                "msg" => "Invalid Coupon!"
            ]);
        }



    }
   /**
     * @Route("ajax/remove/coupon")
     *     *
     * @return JsonResponse
     */
    function removeCoupon()
    {
        $session = new Session();
        $this->session_id = $session->remove('coupon');

        return new JsonResponse([
            "msg" => "Done"
        ]);

    }
}
