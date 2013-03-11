<?php
/**
 * Description of Controller
 * @see http://knws.ru/docs/Controller Documentation of Knws\Controller.
 * @author Barif
 */
namespace Knws;

class Controller
{
    /**
     * run description
     * @see http://knws.ru/docs/Controller/run Documentation of Knws\Controller->run().
     * @return void
     */
    public static function run()
    {

    }

    public function createAction()
    {
        $product = new Product();
        $product->setName('A Foo Bar');
        $product->setPrice('19.99');
        $product->setDescription('Lorem ipsum dolor');

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($product);
        $em->flush();

        return new Response('Created product id '.$product->getId());
    }

    public function showAction($id)
    {
        $product = $this->getDoctrine()
            ->getRepository('AcmeStoreBundle:Product')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        // делает что-нибудь, например передаёт объект $product в шаблон
    }

    
}
