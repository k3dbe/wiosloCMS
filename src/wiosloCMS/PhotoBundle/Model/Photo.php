<?php

namespace wiosloCMS\PhotoBundle\Model;

use wiosloCMS\PhotoBundle\Model\om\BasePhoto;
use wiosloCMS\UserBundle\Model\User;

class Photo extends BasePhoto
{
    public function getRating(\PropelPDO $con = null)
    {
        $rating = parent::getRating($con);

        if (!$rating instanceof Rating) {
            $rating = new Rating();
            $rating->setPlus(0);
            $rating->setMinus(0);
            parent::setRating($rating);
        }

        return $rating;
    }

    public function isOwner(User $user)
    {
        $owner = $this->getUser();

        return $user === $owner;
    }
}
