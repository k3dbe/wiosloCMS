<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use wiosloCMS\PhotoBundle\Crawler\KwejkCrawler;
use wiosloCMS\PhotoBundle\Crawler\WiochaCrawler;

class CrawlerController extends Controller
{
    public function kwejkAction()
    {
        $crawler = new KwejkCrawler();
        $crawler->execute($this->getUser());

        return $this->redirect($this->generateUrl('homepage'));
    }

    public function wiochaAction()
    {
        $crawler = new WiochaCrawler();
        $crawler->execute($this->getUser());

        return $this->redirect($this->generateUrl('homepage'));
    }
}