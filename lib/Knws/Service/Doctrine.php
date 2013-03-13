<?php
/**
 * Description of Doctrine
 * @see http://knws.ru/docs/Service/Doctrine Documentation of Knws/Service\Doctrine.
 * @author Barif
 */

namespace Knws\Service;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Doctrine extends \Knws\Service
{
    public static $instance;
    protected static $class;

    /**
     * Initialize EntityManager
     * return void
     */
    public static function init()
    {
        $isDevMode = true;
        //$config = Setup::createAnnotationMetadataConfiguration(array(APPPATH . "/entities"), $isDevMode);
        $config = Setup::createYAMLMetadataConfiguration(array(APPPATH . "/configs/"), $isDevMode);
        self::$instance = EntityManager::create(\Knws\Instance::$config['config']['Doctrine'], $config);
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

    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $product = $em->getRepository('AcmeStoreBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $product->setName('New product name!');
        $em->flush();

        return $this->redirect($this->generateUrl('homepage'));
    }
    /**
     * methodName description
     * @see http://knws.ru/docs/namespace/methodName Documentation of Knws\namespace->methodName().
     * @param mixed $arg1
     * @return array $result
     */
    public static function DQL($arg1)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery(
            'SELECT p FROM AcmeStoreBundle:Product p WHERE p.price > :price ORDER BY p.price ASC'
        )->setParameter('price', '19.99');
        /*
            ->setParameters(array(
                'price' => '19.99',
                'name'  => 'Foo',
            ))
         */
        try {
            $product = $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $product = null;
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {

        }
        //$products = $query->getResult();
        return $result;
    }

    public static function listRepository()
    {
        $bugs = self::$instance->getRepository('Bug')->getRecentBugs();

        foreach($bugs AS $bug) {
            $r .= $bug->getDescription()." - ".$bug->getCreated()->format('d.m.Y')."\n";
            $r .= "    Reported by: ".$bug->getReporter()->getName()."\n";
            $r .= "    Assigned to: ".$bug->getEngineer()->getName()."\n";
            foreach($bug->getProducts() AS $product) {
                $r .= "    Platform: ".$product->getName()."\n";
            }
            $r .= "\n";
        }

        return $r;
    }
}
