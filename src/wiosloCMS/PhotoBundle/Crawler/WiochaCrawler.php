<?php
namespace wiosloCMS\PhotoBundle\Crawler;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoQuery;
use wiosloCMS\UserBundle\Model\User;

class WiochaCrawler
{
    public function execute(User $owner)
    {
        $client = new Client();

        $crawler = $client->request('GET', 'http://www.wiocha.pl/top_glowna');
        $titles = $crawler->filter(".imageitself")->each(function (Crawler $node, $i) {
            return $node->attr('title');
        });

        $srcs = $crawler->filter(".imageitself")->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        $count = 0;
        foreach ($srcs as $src) {
            if (strpos($src, '.gif') == true) {
                $count++;
                continue;
            }

            if (PhotoQuery::create()->filterByUri($src)->exists()) {
                $count++;
                continue;
            }

            $photo = new Photo();
            $photo->setUser($owner)->setName($titles[$count++])->setUri($src)->save();
        }
    }
}
 