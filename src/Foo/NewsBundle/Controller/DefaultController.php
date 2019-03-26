<?php

namespace Foo\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

//Add News
use Foo\NewsBundle\Entity\News;
use SYmfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/news/", name="foo_news_home")
     *
     */
    public function indexAction()
    {
        $news = $this->getDoctrine()
        		->getRepository('FooNewsBundle:News')
        		->findAll();
        	if (!$news) {
        		throw $this->createNotFoundException('No news found');
        	}

        	$build['news'] = $news;
        	return $this->render('FooNewsBundle:Default:news_show_all.html.twig', $build);
    }

    /**
    * @Route("/news/{id}", name="foo_news_show", requirements={"id"="\d+"})
    */
    public function showAction($id) {
    	$news = $this->getDoctrine()
    			->getRepository('FooNewsBundle:News')
    			->find($id);
    	if (!$news) {
    		throw $this->createNotFoundException('No news found by id ' . $id);
    	}

    	$build['news_item'] = $news;
    	return $this->render('FooNewsBundle:Default:news_show.html.twig', $build);
    }

    /**
    * @Route("/news/add", name="foo_news_add")
    */
    public function addAction(Request $request) {
    	$news = new News();
    	$news->setCreatedDate(new \DateTime());

    	$form = $this->createFormBuilder($news)
    	->add('title', 'text')
    	->add('body', 'text')
    	->add('save', 'submit')
    	->getForm();

    	$form->handleRequest($request);

    	if ($form->isValid()) {
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($news);
    		$em->flush();

    		//adding redirect and flash message
    		$request->getSession()->getFlashBag()->add(
    			'notice',
    			'News added successfully'
    		);
    		return $this->redirect($this->generateUrl('foo_news_home'));
    	}

    	$build['form'] = $form->createView();
    	return $this->render('FooNewsBundle:Default:news_add.html.twig', $build);
    }

    /**
    * @Route("/news/{id}/edit", name="foo_news_edit", requirements={"id"="\d+"})
    */
    public function editAction($id, Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$news = $em->getRepository('FooNewsBundle:News')->find($id);
    	if (!$news) {
    		throw $this->createNotFoundException('No news found for id ' . $id);
    	}

    	$form = $this->createFormBuilder($news)
    		->add('title', 'text')
    		->add('body', 'text')
    		->add('save', 'submit')
    		->getForm();

    	$form->handleRequest($request);

    	if ($form->isValid()) {
    		$em->flush();
    		//return new Response('News updated successfully');
    		//adding redirect and flash message
    		$request->getSession()->getFlashBag()->add(
    			'notice',
    			'News updated successfully'
    		);
    		return $this->redirect($this->generateUrl('foo_news_home'));
    	}

    	$build['form'] = $form->createView();

    	return $this->render('FooNewsBundle:Default:news_add.html.twig', $build);
    }

    /**
    * @Route("/news/{id}/delete", name="foo_news_delete", requirements={"id"="\d+"})
    */
    public function deleteAction($id, Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$news = $em->getRepository('FooNewsBundle:News')->find($id);
    	if (!$news) {
    		throw $this->createNotFoundException(
    			'No news found for id ' .$id);
    	}

    	//with redirecting to form
    	/*$form = $this->createFormBuilder($news)
    	->add('delete', 'submit')
    	->getForm();

    	$form->handleRequest($request);

    	if ($form->isValid()) {
    		$em->remove($news);
    		$em->flush();
    		return new Response('News deleted successfully');
    	}

    	$build['form'] = $form->createView();
    	return $this->render('FooNewsBundle:Default:news_add.html.twig', $build);*/

    	//without redirecting
    	$em->remove($news);
    	$em->flush();
    	//return new Response('News deleted successfully');
    	//adding redirect and flash message
		$request->getSession()->getFlashBag()->add(
			'notice',
			'News deleted successfully'
		);
		return $this->redirect($this->generateUrl('foo_news_home'));
    }
}
