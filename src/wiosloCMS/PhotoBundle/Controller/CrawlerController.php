<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use wiosloCMS\PhotoBundle\Crawler\KwejkCrawler;

class CrawlerController extends Controller
{
    public function kwejkAction()
    {
        $crawler = new KwejkCrawler();
        $crawler->execute($this->getUser());

        return $this->redirect($this->generateUrl('homepage'));
    }
}