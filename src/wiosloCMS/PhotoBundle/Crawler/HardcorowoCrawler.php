<?php
namespace wiosloCMS\PhotoBundle\Crawler;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoQuery;
use wiosloCMS\UserBundle\Model\User;

class HardcorowoCrawler
{
    public function execute(User $owner)
    {
        $client = new Client();

        for ($page = 1; $page <= 10; $page++) {
            $crawler = $client->request('GET', 'http://hardcorowo.pl/najlepsze/' . $page);
            $titles = $crawler->filter(".imgboxWrap img")->each(function (Crawler $node, $i) {
                $title = str_replace(' - 1', '', $node->attr('alt'));
                return $title;
            });

            $srcs = $crawler->filter(".imgboxWrap img")->each(function (Crawler $node, $i) {
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
}
 